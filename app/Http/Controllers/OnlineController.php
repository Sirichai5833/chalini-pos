<?php

namespace App\Http\Controllers;

use App\Models\Category; // ← เพิ่มบรรทัดนี้ถ้าคุณใช้ category
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductStockMovement;
use App\Models\ProductStocks;
use App\Models\ProductUnit;
use App\Models\Sale;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;

class OnlineController extends Controller
{

  public function pagenologin(Request $request)
{
    return view('online.pagenologin');
}
    public function edit(User $member)
    {
        return view('online.edit', compact('member'));
    }

    public function update(Request $request, User $member)
{
    $request->validate([
        'name' =>$request->first_name . ' ' . $request->last_name,
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'phone' => 'required|string|max:20',
        'email' => 'required|email|max:255|unique:users,email,' . $member->id,
        'password' => 'nullable|min:6|confirmed',
    ], [
        'email.unique' => 'อีเมลนี้ถูกใช้ไปแล้ว',
        'password.confirmed' => 'การยืนยันรหัสผ่านไม่ตรงกัน',
    ]);

    // อัปเดตชื่อ
    $member->name = $request->first_name . ' ' . $request->last_name;
    $member->first_name = $request->first_name; // อัปเดตชื่อจริง
    $member->last_name = $request->last_name; // อัปเดตนามสกุล

    // อัปเดตเบอร์โทร
    $member->phone = $request->phone;

    // อัปเดตอีเมล
    $member->email = $request->email;

    // อัปเดตรหัสผ่านเฉพาะเมื่อกรอกใหม่
    if ($request->filled('password')) {
        $member->password = bcrypt($request->password);
    }

    $member->save();

    return redirect()
        ->route('online.index')
        ->with('success', 'ข้อมูลสมาชิกถูกอัปเดตเรียบร้อย');
}

    public function index(Request $request)
{
    $systemAlert = \App\Models\Setting::where('key', 'system_alert')->value('value');
    
    // 1. แคชหมวดหมู่ไว้ 1 ชั่วโมง (ลดการคิวรีฐานข้อมูลทุกครั้งที่รีเฟรชหน้า)
    $categories = cache()->remember('categories', 60 * 60, function () {
        return Category::all();
    });

    $query = Product::query()->with(['productUnits', 'images', 'productStocks.unit']);

    if ($request->filled('category')) {
        $query->where('category_id', $request->category);
    }

    if ($request->filled('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    $products = $query->simplePaginate(10);
    $this->hydrateProductStockTotals($products);

    $products->appends($request->all());

    $cart = session('cart', []);
    $totalItems = collect($cart)->sum(fn($item) => $item['quantity']);

    return view('online.pos', [
        'categories' => $categories,
        'products' => $products,
        'totalItems' => $totalItems,
        'systemAlert' => $systemAlert,
    ]);
}


    private function hydrateProductStockTotals($products): void
    {
        $products->getCollection()->each(function ($product) {
            $product->setAttribute('total_stock', $product->productStocks->sum(function ($stock) use ($product) {
                $unit = $product->productUnits->firstWhere('id', $stock->unit_id);
                $unitQuantity = $unit?->unit_quantity ?? 1;

                return ($stock->store_stock ?? 0) * $unitQuantity;
            }));
        });
    }

    public function add(Request $request)
    {
        $product = Product::with('images')->findOrFail($request->product_id);
        $productUnit = ProductUnit::findOrFail($request->product_unit_id);

        $cart = session()->get('cart', []);
        $key = $request->product_id . '_' . $request->product_unit_id;

        // ตรวจสอบจากตาราง product_images ตามรูปที่คุณส่งมา
        $firstImagePath = null;
        if ($product->images && $product->images->count() > 0) {
            $firstImagePath = $product->images->first()->image_path;
        }

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += 1;
        } else {
            $cart[$key] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'product_unit_id' => $productUnit->id,
                'unit_name' => $productUnit->unit_name,
                'price' => $productUnit->price,
                'quantity' => 1,
                'image' => $firstImagePath, // เก็บค่าจาก image_path ลงใน session
            ];
        }

        session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'total_quantity' => collect(session('cart'))->sum('quantity')
        ]);
    }


// 1. อัปเดตจำนวนสินค้า (แก้ไขแล้ว)
    public function updateQuantity(Request $request, $id)
    {
        $cart = session()->get('cart', []);

        if (!isset($cart[$id])) {
            return response()->json(['success' => false, 'message' => 'ไม่พบสินค้าในตะกร้า'], 404);
        }

        $item = $cart[$id];
        $requestedQty = (int) $request->quantity;

        // ดึงสต็อกจากตาราง product_stocks ตาม product_id และ product_unit_id
        $stock = ProductStocks::where('product_id', $item['product_id'])
                    ->where('unit_id', $item['product_unit_id'])
                    ->first();

        $currentStock = $stock ? $stock->store_stock : 0;

        // เช็คว่าจำนวนที่ขอเกินสต็อกที่มีหรือไม่
        if ($requestedQty > $currentStock) {
            return response()->json([
                'success' => false,
                'message' => "สินค้าในสต็อกมีเหลือเพียง {$currentStock} ชิ้น"
            ], 400);
        }

        // บันทึกจำนวนและสต็อกจากตัวเลขจริงลง Session
        $cart[$id]['quantity'] = $requestedQty;
        $cart[$id]['stock'] = $currentStock;
        
        session()->put('cart', $cart);

        // คำนวณยอดรวมใหม่
        $itemTotal = $cart[$id]['price'] * $requestedQty;
        $total = 0;
        $totalItems = 0;

        foreach ($cart as $cItem) {
            $total += $cItem['price'] * $cItem['quantity'];
            $totalItems += $cItem['quantity'];
        }

        return response()->json([
            'success' => true,
            'quantity' => $requestedQty,
            'item_total' => number_format($itemTotal, 2),
            'total' => number_format($total, 2),
            'total_items' => $totalItems
        ]);
    }

    // 2. แสดงหน้าตะกร้าสินค้า (แก้ไขแล้ว)
    public function cart()
    {
        $cart = session()->get('cart', []);
        $total = 0;
        $outOfStockItems = [];

        foreach ($cart as $key => &$item) {
            // ดึงสต็อกจากตาราง product_stocks ตามหน่วยของสินค้านั้นๆ
            $stock = ProductStocks::where('product_id', $item['product_id'])
                        ->where('unit_id', $item['product_unit_id'])
                        ->first();

            $stockAmount = $stock ? $stock->store_stock : 0;

            $item['stock'] = $stockAmount; // เก็บจำนวนสต็อกที่เป็นตัวเลขลง Session
            $total += $item['price'] * $item['quantity'];

            // เช็คว่าสินค้าชิ้นไหนสั่งเกินสต็อก
            if ($item['quantity'] > $stockAmount) {
                $outOfStockItems[] = [
                    'name' => $item['name'] . " ({$item['unit_name']})",
                    'requested' => $item['quantity'],
                    'available' => $stockAmount
                ];
            }
        }

        session()->put('cart', $cart);

        return view('online.cart', compact('cart', 'total', 'outOfStockItems'));
    }
    public function showCheckoutForm()
{
    $productUnit = ProductUnit::all();
    $member = Auth::user();

    // ดึงข้อมูลสินค้าจาก session
    $cart = session()->get('cart', []);
    $total = 0;
    $smallestUnitId = null;

    foreach ($cart as $item) {
        $total += $item['price'] * $item['quantity'];
    }

    if (!empty($cart)) {
        $firstItem = reset($cart);
        $productId = $firstItem['product_id'];

        // หาหน่วยที่เล็กที่สุดของสินค้านี้
        $smallestUnit = ProductUnit::where('product_id', $productId)
            ->orderBy('unit_quantity', 'asc')
            ->first();

        if ($smallestUnit) {
            $smallestUnitId = $smallestUnit->id;
        }
    }

    // ✅ เพิ่ม 'cart' เข้าไปใน compact()
    return view('online.checkout', compact('cart', 'member', 'total', 'smallestUnitId', 'productUnit'));
}




    public function processCheckout(Request $request)
    {
        $request->validate([
            'order_token' => 'required|uuid', // ✅ เพิ่ม
            'phone' => 'required',
            'payment_method' => 'required',
            'slip' => 'required_if:payment_method,โอนผ่านบัญชีธนาคาร|image|max:2048',
        ]);
        if (Order::where('order_token', $request->order_token)->exists()) {
            return redirect('/online/track')
                ->with('info', 'คำสั่งซื้อนี้ถูกบันทึกแล้ว');
        }
        $member = Auth::user();
        $cartItems = session('cart', []);
        $totalAmount = collect($cartItems)->sum(fn($item) => $item['price'] * $item['quantity']);

        $slipPath = $request->hasFile('slip')
            ? $request->file('slip')->store('slips', 'public')
            : null;

        DB::beginTransaction();

        try {
            $order = Order::create([
                'order_token' => $request->order_token,
                'order_code' => 'ORD' . mt_rand(1000, 9999),
                'user_id' => $member?->id,
                'payment_method' => $request->payment_method,
                'status' => 'รอเจ้าหน้าที่รับคำสั่งซื้อ',
                'tracking_number' => $request->phone,
                'slip_path' => $slipPath,
                'total_amount' => $totalAmount,
            ]);

            foreach ($cartItems as $item) {
                $unit = ProductUnit::findOrFail($item['product_unit_id']);
                $productId = $unit->product_id;
                $unitQty = $unit->unit_quantity;
                $qty = (int) $item['quantity'];
                $totalBaseQty = $qty * $unitQty;

                $baseUnit = ProductUnit::where('product_id', $productId)
                    ->orderBy('unit_quantity', 'asc')
                    ->first();

                // Step 1: หักจากหน่วยที่เลือก
                $stock = ProductStocks::where('product_id', $productId)
                    ->where('unit_id', $unit->id)
                    ->first();

                $availableQty = $stock?->store_stock ?? 0;
                $deductQty = min($qty, $availableQty);
                $remainingQty = $qty - $deductQty;

                if ($deductQty > 0 && $stock) {
                    $stock->decrement('store_stock', $deductQty);
                    ProductStockMovement::create([
                        'product_id' => $productId,
                        'order_id' => $order->id, // ✅ เพิ่ม order_id
                        'type' => 'out',
                        'quantity' => $deductQty,
                        'unit_quantity' => $unitQty,
                        'unit' => $unit->unit_name,
                        'location' => 'store',
                        'note' => 'ขายจากหน่วยที่เลือก (online)',
                    ]);
                }

                // Step 2: ถ้าไม่พอ → แปลงจากหน่วยอื่น
                if ($remainingQty > 0) {
                    $remainingBaseQty = $remainingQty * $unitQty;

                    $otherUnits = ProductUnit::where('product_id', $productId)
                        ->where('id', '!=', $unit->id)
                        ->orderBy('unit_quantity', 'asc')
                        ->get();
                    $convertedBaseQty = 0; // ← เพิ่มบรรทัดนี้
                    foreach ($otherUnits as $otherUnit) {
                        $stockOther = ProductStocks::where('product_id', $productId)
                            ->where('unit_id', $otherUnit->id)
                            ->first();

                        $available = $stockOther?->store_stock ?? 0;
                        $basePerUnit = $otherUnit->unit_quantity;

                        if ($basePerUnit == 0 || $available == 0) continue;

                        $neededUnit = ceil($remainingBaseQty / $basePerUnit);
                        $usableQty = min($available, $neededUnit);
                        $convertedBase = $usableQty * $basePerUnit;
                        $convertedBaseQty += $convertedBase;
                        $remainingBaseQty -= $convertedBase;

                        if ($usableQty > 0) {
                            $stockOther->decrement('store_stock', $usableQty);

                            // เพิ่มเข้า base unit ก่อน (simulate การแตกหน่วย)
                            if ($baseUnit) {
                                $baseStock = ProductStocks::firstOrCreate([
                                    'product_id' => $productId,
                                    'unit_id' => $baseUnit->id,
                                ]);

                                $baseStock->increment('store_stock', $convertedBase);
                            }

                            ProductStockMovement::create([
                                'product_id' => $productId,
                                'order_id' => $order->id,
                                'type' => 'out',
                                'quantity' => $usableQty,
                                'unit_quantity' => $basePerUnit,
                                'unit' => $otherUnit->unit_name,
                                'location' => 'store',
                                'note' => "แตกหน่วยจาก {$otherUnit->unit_name} (online)",
                            ]);
                        }


                        if ($remainingBaseQty <= 0) break;
                    }

                    if ($remainingBaseQty > 0) {
                        throw new \Exception("สินค้ารหัส {$productId} สต็อกไม่พอ แม้พยายามแปลงหน่วยแล้ว");
                    }

                    $deductFromBase = $remainingQty * $unitQty;
                    $finalBaseStock = ProductStocks::where('product_id', $productId)
                        ->where('unit_id', $baseUnit->id)
                        ->first();

                    if ($deductFromBase > 0) {
                        if (!$finalBaseStock || $finalBaseStock->store_stock < $deductFromBase) {
                            throw new \Exception("สต็อกไม่พอที่จะขาย หลังจากแปลงหน่วยแล้ว");
                        }

                        $finalBaseStock->decrement('store_stock', $deductFromBase);
                        ProductStockMovement::create([
                            'product_id' => $productId,
                            'order_id' => $order->id, // ✅ เพิ่ม order_id
                            'type' => 'out',
                            'quantity' => $deductFromBase,
                            'unit_quantity' => $baseUnit->unit_quantity,
                            'unit' => $baseUnit->unit_name,
                            'location' => 'store',
                            'note' => 'ขายสินค้าหลังแปลงหน่วย (online)',
                        ]);
                    }
                }
                // สร้างรายการสินค้า
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_unit_id' => $unit->id,
                    'quantity' => $qty,
                    'price' => $item['price'],
                    'total' => $item['price'] * $qty,
                    'cost' => $unit->cost_price,
                ]);
            }

            DB::commit();

            session()->forget('cart');
            session()->forget('checkout_items');

            return redirect('/online/track')->with('success', 'สั่งซื้อสำเร็จ');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['cart' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
        }
    }

    public function generateQRCode(Request $request)
    {
        $amount = $request->query('amount');

        if (!is_numeric($amount)) {
            abort(400, 'Invalid amount');
        }

        $amount = number_format($amount, 2, '.', '');
        $promptPayID = Setting::get('promptpay', env('PROMPTPAY_ID'));

        return redirect("https://promptpay.io/{$promptPayID}/{$amount}");
    }

    public function updateAlert(Request $request)
    {
        $request->validate([
            'system_alert' => 'nullable|string|max:255',
        ]);

        DB::table('settings')->updateOrInsert(
            ['key' => 'system_alert'],
            ['value' => $request->input('system_alert')]
        );

        return redirect()->back()->with('success', 'อัปเดตข้อความแจ้งเตือนเรียบร้อย!');
    }

    public function remove($id)
{
    $cart = session()->get('cart', []);

    if (isset($cart[$id])) {
        unset($cart[$id]);
    }

    session()->put('cart', $cart);

    $total = collect($cart)->sum(function ($item) {
        return $item['price'] * $item['quantity'];
    });

    $totalItems = collect($cart)->sum('quantity');

    return response()->json([
        'success' => true,
        'total' => number_format($total, 2),
        'total_items' => $totalItems,
    ]);
}
}
