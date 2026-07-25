<?php

namespace App\Http\Controllers;

use App\Models\Category; // ← เพิ่มบรรทัดนี้ถ้าคุณใช้ category
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductStocks;
use App\Models\ProductUnit;
use App\Models\Sale;
use App\Models\Setting;
use App\Models\User;
use App\Services\StockDeductionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Facades\Image;

class OnlineController extends Controller
{

  public function pagenologin(Request $request)
{
    return view('online.pagenologin');
}
    public function edit(User $member)
    {
        $this->authorizeSelfOrAdmin($member);

        return view('online.edit', compact('member'));
    }

    public function update(Request $request, User $member)
{
    $this->authorizeSelfOrAdmin($member);

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
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'product_unit_id' =>
                'required|integer|exists:product_units,id',
        ]);
        $product = Product::with('images')
            ->whereKey($validated['product_id'])
            ->where('is_active', true)
            ->where('is_online', true)
            ->firstOrFail();
        $productUnit = ProductUnit::query()
            ->whereKey($validated['product_unit_id'])
            ->where('product_id', $product->id)
            ->firstOrFail();

        $cart = session()->get('cart', []);
        $this->assertCartOwnership($cart);
        $key = $product->id . '_' . $productUnit->id;

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
        session()->put('cart_owner_id', Auth::id());

        return response()->json([
            'success' => true,
            'total_quantity' => collect(session('cart'))->sum('quantity')
        ]);
    }


// 1. อัปเดตจำนวนสินค้า (แก้ไขแล้ว)
    public function updateQuantity(Request $request, $id)
    {
        $cart = session()->get('cart', []);
        $this->assertCartOwnership($cart);
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        if (!isset($cart[$id])) {
            return response()->json(['success' => false, 'message' => 'ไม่พบสินค้าในตะกร้า'], 404);
        }

        $item = $cart[$id];
        $requestedQty = (int) $validated['quantity'];

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
        $this->assertCartOwnership($cart);
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
    $this->assertCartOwnership($cart);
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




    public function processCheckout(
        Request $request,
        StockDeductionService $stockDeduction
    )
    {
        $validated = $request->validate([
            'order_token' => 'required|uuid',
            'phone' => 'required|string|max:20',
            'payment_method' => [
                'required',
                'in:โอนผ่านบัญชีธนาคาร,เก็บเงินปลายทาง',
            ],
            'slip' =>
                'required_if:payment_method,โอนผ่านบัญชีธนาคาร|image|max:2048',
        ]);
        $memberId = Auth::id();
        $cartItems = session('cart', []);
        $cartOwnerId = session('cart_owner_id');
        $slipPath = null;

        try {
            $result = DB::transaction(function () use (
                $request,
                $validated,
                $memberId,
                $cartItems,
                $cartOwnerId,
                $stockDeduction,
                &$slipPath
            ) {
                $member = User::query()
                    ->lockForUpdate()
                    ->findOrFail($memberId);
                $existingOrder = Order::query()
                    ->where('order_token', $validated['order_token'])
                    ->lockForUpdate()
                    ->first();

                if ($existingOrder) {
                    abort_unless(
                        (int) $existingOrder->user_id === (int) $member->id,
                        403
                    );

                    if (
                        in_array(
                            $existingOrder->status,
                            ['cancelled', 'ยกเลิก'],
                            true
                        )
                    ) {
                        throw ValidationException::withMessages([
                            'order_token' =>
                                'Cancelled order tokens cannot be reused.',
                        ]);
                    }

                    return [
                        'order' => $existingOrder,
                        'created' => false,
                    ];
                }

                if (
                    empty($cartItems) ||
                    (int) $cartOwnerId !== (int) $member->id
                ) {
                    throw ValidationException::withMessages([
                        'cart' => 'Cart is empty or belongs to another user.',
                    ]);
                }

                $items = $this->checkoutItems($cartItems);
                $totalAmount = round($items->sum('subtotal'), 2);

                if ($totalAmount <= 0) {
                    throw ValidationException::withMessages([
                        'cart' => 'Order total must be greater than zero.',
                    ]);
                }

                if ($request->hasFile('slip')) {
                    $slipPath = $request
                        ->file('slip')
                        ->store('slips', 'public');
                }

                $tokenCode = strtoupper(str_replace(
                    '-',
                    '',
                    $validated['order_token']
                ));
                $order = Order::create([
                    'order_token' => $validated['order_token'],
                    'order_code' => 'ORD' . substr($tokenCode, 0, 16),
                    'user_id' => $member->id,
                    'payment_method' => $validated['payment_method'],
                    'status' => 'รอเจ้าหน้าที่รับคำสั่งซื้อ',
                    'tracking_number' => $validated['phone'],
                    'slip_path' => $slipPath,
                    'total_amount' => $totalAmount,
                ]);

                foreach ($items as $item) {
                    $unit = $item['unit'];

                    $stockDeduction->deduct(
                        $unit,
                        $item['quantity'],
                        'ขายสินค้าออนไลน์',
                        $order->id
                    );

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_unit_id' => $unit->id,
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'total' => $item['subtotal'],
                        'cost' => $unit->cost_price,
                    ]);
                }

                return [
                    'order' => $order,
                    'created' => true,
                ];
            });
        } catch (\Throwable $exception) {
            if ($slipPath) {
                Storage::disk('public')->delete($slipPath);
            }

            throw $exception;
        }

        if ($result['created']) {
            session()->forget([
                'cart',
                'checkout_items',
                'cart_owner_id',
            ]);
        }

        return redirect('/online/track')->with(
            $result['created'] ? 'success' : 'info',
            $result['created']
                ? 'สั่งซื้อสำเร็จ'
                : 'คำสั่งซื้อนี้ถูกบันทึกแล้ว'
        );
    }

    private function checkoutItems(array $cartItems)
    {
        $quantities = [];

        foreach ($cartItems as $item) {
            if (!is_array($item)) {
                throw ValidationException::withMessages([
                    'cart' => 'A cart item is invalid.',
                ]);
            }

            $unitId = filter_var(
                $item['product_unit_id'] ?? null,
                FILTER_VALIDATE_INT
            );
            $quantity = filter_var(
                $item['quantity'] ?? null,
                FILTER_VALIDATE_INT
            );

            if (!$unitId || !$quantity || $quantity < 1) {
                throw ValidationException::withMessages([
                    'cart' => 'Cart quantities and product units must be valid.',
                ]);
            }

            $quantities[$unitId] = ($quantities[$unitId] ?? 0) + $quantity;
        }

        ksort($quantities);

        $units = ProductUnit::query()
            ->with('product')
            ->whereIn('id', array_keys($quantities))
            ->orderBy('product_id')
            ->orderBy('unit_quantity')
            ->orderBy('id')
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        if ($units->count() !== count($quantities)) {
            throw ValidationException::withMessages([
                'cart' => 'A cart product unit no longer exists.',
            ]);
        }

        return collect($quantities)->map(function (
            int $quantity,
            int $unitId
        ) use ($units) {
            $unit = $units->get($unitId);

            if (
                !$unit->product ||
                !$unit->product->is_active ||
                !$unit->product->is_online ||
                $unit->price === null ||
                (float) $unit->price < 0
            ) {
                throw ValidationException::withMessages([
                    'cart' => 'A cart product is not available for online sale.',
                ]);
            }

            $price = (float) $unit->price;

            return [
                'unit' => $unit,
                'quantity' => $quantity,
                'price' => $price,
                'subtotal' => round($price * $quantity, 2),
            ];
        })->values();
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
    $this->assertCartOwnership($cart);

    if (isset($cart[$id])) {
        unset($cart[$id]);
    }

    session()->put('cart', $cart);

    if (empty($cart)) {
        session()->forget('cart_owner_id');
    }

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

private function assertCartOwnership(array $cart): void
{
    if (empty($cart)) {
        return;
    }

    abort_unless(
        Auth::check() &&
            (int) session('cart_owner_id') === (int) Auth::id(),
        403
    );
}

private function authorizeSelfOrAdmin(User $member): void
{
    $actor = auth()->user();

    abort_unless(
        $actor && ($actor->role === 'admin' || $actor->is($member)),
        403
    );
}
}
