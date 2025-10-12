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
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OnlineController extends Controller
{

    public function pagenologin(Request $request)
{
    $categories = Category::all();

    $query = Product::with('productUnits');

    if ($request->filled('category')) {
        $query->where('category_id', $request->category);
    }

    if ($request->filled('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    $products = $query->paginate(10);
    $products->appends($request->all());

    $cart = session('cart', []);

    $totalItems = collect($cart)->sum(fn($item) => $item['quantity']);

    return view('online.pagenologin', [
        'categories' => $categories,
        'products' => $products,
        'totalItems' => $totalItems,
    ]);
}

    public function edit(User $member)
    {
        return view('online.edit', compact('member'));
    }

    public function update(Request $request, User $member)
    {
        // Validate the incoming request

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $member->id, // ตรวจสอบว่าอีเมลไม่ซ้ำ
            'password' => 'nullable|min:6|confirmed',
        ], [
            'email.unique' => 'อีเมลนี้ถูกใช้ไปแล้ว',
            'password.confirmed' => 'การยืนยันรหัสผ่านไม่ตรงกัน',
        ]);

        // อัปเดตข้อมูลสมาชิก
        $member->name = $request->name;
        $member->email = $request->email;

        // อัปเดตรหัสผ่านถ้ามีการเปลี่ยน
        if ($request->password) {
            $member->password = bcrypt($request->password);
        }

        $member->save();

        return redirect()->route('online.index')->with('success', 'ข้อมูลสมาชิกถูกอัปเดตเรียบร้อย');
    }

    public function index(Request $request)
{
      $systemAlert = \App\Models\Setting::where('key', 'system_alert')->value('value');
    $categories = Category::all();

    $query = Product::with('productUnits');

    if ($request->filled('category')) {
        $query->where('category_id', $request->category);
    }

    if ($request->filled('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    $products = $query->paginate(10);
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


    public function add(Request $request)
    {
         $product = Product::findOrFail($request->product_id);
    $productUnit = ProductUnit::findOrFail($request->product_unit_id);

    $cart = session()->get('cart', []);

    $key = $request->product_id . '_' . $request->product_unit_id; // กำหนด key ให้เฉพาะเจาะจงหน่วยด้วย

    if(isset($cart[$key])) {
        $cart[$key]['quantity'] += 1;
    } else {
        $cart[$key] = [
            'product_id' => $product->id,
            'name' => $product->name,
            'product_unit_id' => $productUnit->id,
            'unit_name' => $productUnit->unit_name,  // เก็บชื่อหน่วยตรงนี้เลย
            'price' => $productUnit->price,
            'quantity' => 1,
            'image' => $product->image,
        ];
    }

    session()->put('cart', $cart);

   return response()->json([
    'success' => true,
    'total_quantity' => collect(session('cart'))->sum('quantity')
]);

}


    public function cart()
    {
        // ดึงข้อมูลสินค้าจากตะกร้า
        $cart = session()->get('cart', []);
        // คำนวณยอดรวม
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        // ส่งข้อมูลไปยัง view
        return view('online.cart', compact('cart', 'total'));
    }

public function showCheckoutForm()
{
    $productUnit = ProductUnit::all();
    $member = Auth::user();

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

    return view('online.checkout', compact('member', 'total', 'smallestUnitId', 'productUnit'));
}




   public function processCheckout(Request $request)
{
    $request->validate([
        'phone' => 'required',
        'payment_method' => 'required',
        'slip' => 'required_if:payment_method,โอนผ่านบัญชีธนาคาร|image|max:2048',
    ]);

    $member = Auth::user();
    $cartItems = session('cart', []);
    $totalAmount = collect($cartItems)->sum(fn($item) => $item['price'] * $item['quantity']);

    $slipPath = $request->hasFile('slip')
        ? $request->file('slip')->store('slips', 'public')
        : null;

    DB::beginTransaction();

    try {
        $order = Order::create([
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
    $order = Order::where('user_id', Auth::id())->latest()->first();

    if (!$order) {
        abort(404, 'Order not found');
    }

    $amount = number_format($order->total, 2, '.', '');
    $promptPayID = env('PROMPTPAY_ID');

    // ✅ ตอบกลับเป็นภาพ QR จาก promptpay.io (ให้ browser แสดงตรง ๆ)
    return redirect("https://promptpay.io/{$promptPayID}/{$amount}");
}



    public function remove($id)
{
    $cart = session()->get('cart', []);

    if (isset($cart[$id])) {
        unset($cart[$id]); // เอาสินค้าออกจาก array
        session()->put('cart', $cart); // เซฟตะกร้ากลับเข้า session ใหม่
    }

    return back()->with('success', 'ลบสินค้าออกจากตะกร้าแล้ว');
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
}
