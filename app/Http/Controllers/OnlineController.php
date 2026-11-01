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
    $settings = DB::table('settings')
        ->pluck('value', 'key');

    $shopSettings = (object) [
        'shop_name' => $settings->get('shop_name', 'POS'),
        'phone' => $settings->get('phone'),
        'promptpay' => $settings->get('promptpay'),
        'open_time' => $settings->get('open_time', '09:00'),
        'close_time' => $settings->get('close_time', '21:00'),
        'opening_cash' => $settings->get('opening_cash', 0),
        'shipping_fee' => (float) $settings->get('shipping_fee', 0),
    ];

    $systemAlert = $settings->get('system_alert');

    $categories = cache()->remember(
        'online_categories',
        now()->addHour(),
        function () {
            return Category::query()
                ->orderBy('name')
                ->get();
        }
    );

    $query = Product::query()
        ->where('is_active', true)
        ->where('is_online', true)
        ->with([
            'productUnits',
            'images',
            'productStocks.unit',
        ]);

    if ($request->filled('category')) {
        $query->where(
            'category_id',
            $request->integer('category')
        );
    }

    if ($request->filled('search')) {
        $search = trim((string) $request->input('search'));

        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhereHas('productUnits', function ($unitQuery) use ($search) {
                    $unitQuery->where('barcode', 'like', "%{$search}%");
                });
        });
    }

    $products = $query
        ->orderBy('name')
        ->simplePaginate(12)
        ->withQueryString();

    $this->hydrateProductStockTotals($products);

    $cart = session('cart', []);

    $totalItems = collect($cart)->sum(
        fn ($item) => (int) ($item['quantity'] ?? 0)
    );

    return view('online.pos', [
        'categories' => $categories,
        'products' => $products,
        'totalItems' => $totalItems,
        'systemAlert' => $systemAlert,
        'shopSettings' => $shopSettings,
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



    /**
     * รวมสต็อกหน้าร้านของสินค้าตัวเดียวกันจากทุกหน่วย
     * โดยแปลงทุกหน่วยเป็นหน่วยฐานก่อน แล้วแปลงกลับเป็นหน่วยที่ลูกค้าเลือก
     */
    private function availableStockForUnit(int $productId, int $selectedUnitId): int
    {
        $units = ProductUnit::query()
            ->where('product_id', $productId)
            ->get(['id', 'unit_quantity'])
            ->keyBy('id');

        $selectedUnit = $units->get($selectedUnitId);

        if (!$selectedUnit) {
            return 0;
        }

        $selectedUnitQuantity = max(
            1,
            (int) ($selectedUnit->unit_quantity ?? 1)
        );

        $stocks = ProductStocks::query()
            ->where('product_id', $productId)
            ->get(['unit_id', 'store_stock']);

        $totalBaseUnits = $stocks->sum(function ($stock) use ($units) {
            $unit = $units->get($stock->unit_id);

            $unitQuantity = max(
                1,
                (int) ($unit?->unit_quantity ?? 1)
            );

            return max(
                0,
                (int) ($stock->store_stock ?? 0)
            ) * $unitQuantity;
        });

        return intdiv(
            (int) $totalBaseUnits,
            $selectedUnitQuantity
        );
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
        return response()->json([
            'success' => false,
            'message' => 'ไม่พบสินค้าในตะกร้า',
        ], 404);
    }

    $item = $cart[$id];
    $requestedQty = (int) $validated['quantity'];

    // รวมสต็อกของสินค้าตัวเดียวกันจากทุกหน่วย
    $currentStock = $this->availableStockForUnit(
        (int) $item['product_id'],
        (int) $item['product_unit_id']
    );

    if ($requestedQty > $currentStock) {
        return response()->json([
            'success' => false,
            'message' => "สินค้ารวมทุกหน่วยมีคงเหลือเทียบเท่า {$currentStock} {$item['unit_name']}",
            'stock' => $currentStock,
        ], 400);
    }

    // อัปเดตจำนวนสินค้า
    $cart[$id]['quantity'] = $requestedQty;
    $cart[$id]['stock'] = $currentStock;
    $cart[$id]['available_stock'] = $currentStock;

    session()->put('cart', $cart);

    // คำนวณยอดสินค้าใหม่
    $itemTotal = (float) $cart[$id]['price'] * $requestedQty;

    $total = 0;
    $totalItems = 0;

    foreach ($cart as $cItem) {
        $total += (float) $cItem['price'] * (int) $cItem['quantity'];
        $totalItems += (int) $cItem['quantity'];
    }

    // คำนวณค่าจัดส่งและเงื่อนไขส่งฟรี
    $shipping = $this->calculateShipping($total);

    return response()->json([
        'success' => true,
        'quantity' => $requestedQty,
        'stock' => $currentStock,
        'item_total' => number_format($itemTotal, 2, '.', ''),
        'total' => number_format($total, 2, '.', ''),
        'total_items' => $totalItems,

        // ข้อมูลค่าจัดส่ง
        'shipping_fee' => number_format($shipping['shippingFee'], 2, '.', ''),
        'regular_shipping_fee' => number_format($shipping['regularShippingFee'], 2, '.', ''),
        'is_free_shipping' => $shipping['isFreeShipping'],
        'remaining_for_free_shipping' => number_format($shipping['remainingForFreeShipping'], 2, '.', ''),
        'grand_total' => number_format($shipping['grandTotal'], 2, '.', ''),
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
        // รวมสต็อกของสินค้าตัวเดียวกันจากทุกหน่วย
        // แล้วแปลงเป็นจำนวนของหน่วยที่อยู่ในตะกร้า
        $stockAmount = $this->availableStockForUnit(
            (int) $item['product_id'],
            (int) $item['product_unit_id']
        );

        $item['stock'] = $stockAmount;
        $item['available_stock'] = $stockAmount;

        $total +=
            (float) $item['price']
            * (int) $item['quantity'];

        if ((int) $item['quantity'] > $stockAmount) {
            $outOfStockItems[] = [
                'name' =>
                    $item['name']
                    . " ({$item['unit_name']})",
                'requested' => (int) $item['quantity'],
                'available' => $stockAmount,
            ];
        }
    }

    unset($item);

    session()->put('cart', $cart);

    $shipping = $this->calculateShipping($total);

$shippingFee = $shipping['shippingFee'];
$grandTotal = $shipping['grandTotal'];

    return view('online.cart', array_merge(
    compact('cart', 'total', 'shippingFee', 'grandTotal', 'outOfStockItems'),
    $shipping
));
}

    public function showCheckoutForm()
    {
        $productUnit = ProductUnit::all();
        $member = Auth::user();

        $cart = session()->get('cart', []);
        $this->assertCartOwnership($cart);

        $total = collect($cart)->sum(function ($item) {
            return (float) ($item['price'] ?? 0)
                * (int) ($item['quantity'] ?? 0);
        });

        $shipping = $this->calculateShipping($total);

$shippingFee = $shipping['shippingFee'];
$grandTotal = $shipping['grandTotal'];
        $smallestUnitId = null;

        if (!empty($cart)) {
            $firstItem = reset($cart);
            $productId = $firstItem['product_id'];

            $smallestUnit = ProductUnit::query()
                ->where('product_id', $productId)
                ->orderBy('unit_quantity')
                ->first();

            if ($smallestUnit) {
                $smallestUnitId = $smallestUnit->id;
            }
        }

        return view('online.checkout', array_merge(
    compact('cart', 'member', 'total', 'shippingFee', 'grandTotal', 'smallestUnitId', 'productUnit'),
    $shipping
));
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
                $itemsTotal = round($items->sum('subtotal'), 2);
                $shipping = $this->calculateShipping($itemsTotal);

$shippingFee = $shipping['shippingFee'];
$totalAmount = $shipping['grandTotal'];

                if ($itemsTotal <= 0) {
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
                $orderData = [
                    'order_token' => $validated['order_token'],
                    'order_code' => 'ORD' . substr($tokenCode, 0, 16),
                    'user_id' => $member->id,
                    'payment_method' => $validated['payment_method'],
                    'status' => 'รอเจ้าหน้าที่รับคำสั่งซื้อ',
                    'tracking_number' => $validated['phone'],
                    'slip_path' => $slipPath,
                    "shipping_fee" => $shippingFee,
                    'items_total' => $itemsTotal,
                    'total_amount' => $totalAmount,
                ];

                if (\Illuminate\Support\Facades\Schema::hasColumn(
                    'orders',
                    'shipping_fee'
                )) {
                    $orderData['shipping_fee'] = $shippingFee;
                }

                $order = Order::create($orderData);

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

    public function history(Request $request)
    {
        $search = trim((string) $request->input('search', ''));

        $orders = Order::query()
            ->with('user')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('id', 'like', "%{$search}%")
                        ->orWhere('order_code', 'like', "%{$search}%")
                        ->orWhere('tracking_number', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery
                                ->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%");
                        });
                });
            })
            ->when(
                $request->filled('status'),
                fn ($query) => $query->where(
                    'status',
                    $request->input('status')
                )
            )
            ->when(
                $request->filled('payment_method'),
                fn ($query) => $query->where(
                    'payment_method',
                    $request->input('payment_method')
                )
            )
            ->when(
                $request->filled('date_from'),
                fn ($query) => $query->whereDate(
                    'created_at',
                    '>=',
                    $request->input('date_from')
                )
            )
            ->when(
                $request->filled('date_to'),
                fn ($query) => $query->whereDate(
                    'created_at',
                    '<=',
                    $request->input('date_to')
                )
            )
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view(
            'online.orders.history',
            compact('orders')
        );
    }

    /**
     * แสดงรายละเอียดคำสั่งซื้อออนไลน์ 1 รายการ
     *
     * Controller ส่งตัวแปร $order เพียงตัวเดียว
     * ดังนั้นหน้า Blade ต้องใช้ $order โดยตรง
     * และวนเฉพาะ $order->items เท่านั้น
     */
    public function showOrder(Order $order)
    {
        $order->load([
            'user',
            'items.productUnit.product',
        ]);

        return view(
            'sale.show-order',
            compact('order')
        );
    }

    /**
     * ใช้กับ route ชื่อ online.orders.show
     * และ View resources/views/online/orders/show.blade.php
     */
    public function show(Order $order)
    {
        $order->load([
            'user',
            'items.productUnit.product',
        ]);

        return view(
            'online.orders.show',
            compact('order')
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

   $shipping = $this->calculateShipping($total);

return response()->json([
    'success' => true,
    'total' => number_format($total, 2, '.', ''),
    'total_items' => $totalItems,

    'shipping_fee' => number_format($shipping['shippingFee'], 2, '.', ''),
    'regular_shipping_fee' => number_format($shipping['regularShippingFee'], 2, '.', ''),
    'is_free_shipping' => $shipping['isFreeShipping'],
    'remaining_for_free_shipping' => number_format(
        $shipping['remainingForFreeShipping'],
        2,
        '.',
        ''
    ),
    'grand_total' => number_format($shipping['grandTotal'], 2, '.', ''),
]);
}

private function calculateShipping(float $subtotal): array
{
    $settings = DB::table('settings')
        ->whereIn('key', [
            'shipping_fee',
            'shipping_free_enabled',
            'shipping_free_minimum',
        ])
        ->pluck('value', 'key');

    $regularFee = max(0, (float) $settings->get('shipping_fee', 0));

    $freeEnabled = in_array(
        strtolower((string) $settings->get('shipping_free_enabled', '0')),
        ['1', 'true', 'yes', 'on'],
        true
    );

    $minimum = max(
        0,
        (float) $settings->get('shipping_free_minimum', 300)
    );

    $isFree = $freeEnabled && $subtotal >= $minimum;

    $fee = $isFree ? 0 : $regularFee;

    return [
        'shippingFee' => $fee,
        'regularShippingFee' => $regularFee,
        'isFreeShipping' => $isFree,
        'freeShippingEnabled' => $freeEnabled,
        'freeShippingMinimum' => $minimum,
        'remainingForFreeShipping' => $freeEnabled && !$isFree
            ? max(0, $minimum - $subtotal)
            : 0,
        'grandTotal' => round($subtotal + $fee, 2),
    ];
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