<?php

namespace App\Http\Controllers;

use App\Models\Category; // ← เพิ่มบรรทัดนี้ถ้าคุณใช้ category
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductStocks;
use App\Models\ProductUnit;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OnlineController extends Controller
{




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
        $product_unit_id = null;

        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        if (!empty($cart)) {
            // สมมติเอา product_unit_id จากสินค้าชิ้นแรก
            $firstItem = reset($cart);
              $product_unit_id = ProductUnit::find($firstItem['product_unit_id']);
        }

        return view('online.checkout', compact('member', 'total', 'product_unit_id', 'productUnit'));
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
    $totalAmount = collect($cartItems)->sum(function ($item) {
        return $item['price'] * $item['quantity'];
    });

    // อัปโหลดสลิปถ้ามี
    if ($request->hasFile('slip')) {
    $slipPath = $request->file('slip')->store('slips', 'public');
} else {
    $slipPath = null;
}


    // ตรวจสอบสต๊อกก่อนสร้างออเดอร์
   foreach ($cartItems as $item) {
    $productUnitId = $item['product_unit_id'] ?? null;
    $quantity = (int) $item['quantity'];

    if (!$productUnitId) {
        return back()->withErrors(['cart' => 'ไม่พบหน่วยสินค้าในตะกร้า กรุณาลองใหม่อีกครั้ง']);
    }
    
    $unit = ProductUnit::with('product.productStocks')->find($productUnitId);
    if (!$unit || !$unit->product_id) {
        return back()->withErrors(['cart' => 'ไม่พบสินค้าในระบบหรือไม่มีข้อมูลสต๊อก']);
    }   
    
$availableStock = $unit->product->productStocks->sum('store_stock');
   if ($availableStock < $quantity) {
    return back()->withErrors([
        'cart' => "สินค้า '{$unit->product->name}' สต๊อกไม่เพียงพอ (คงเหลือ {$availableStock}, ต้องการ {$quantity})"
    ]);
}
}


    // สร้างคำสั่งซื้อ
    $order = Order::create([
        'order_code' => 'ORD' . mt_rand(1000, 9999),
        'user_id' => $member ? $member->id : null,
        'payment_method' => $request->payment_method,
        'status' => 'pending',
        'tracking_number' => $request->phone,
        'slip_path' => $slipPath,
        'total_amount' => $totalAmount,
    ]);

    // สร้างรายการสินค้าในคำสั่งซื้อ พร้อมหักสต๊อก
    foreach ($cartItems as $item) {
        $unit = ProductUnit::find($item['product_unit_id']);

        OrderItem::create([
            'order_id' => $order->id,
            'product_unit_id' => $item['product_unit_id'],
            'quantity' => $item['quantity'],
            'price' => $item['price'],
            'total' => $item['price'] * $item['quantity'],
        ]);

        $productStock = ProductStocks::where('product_id', $unit->product_id)->first();

if ($productStock) {
    $productStock->store_stock -= $item['quantity'];
    $productStock->save();
}
    }

    // ล้างตะกร้า
    session()->forget('cart');
    session()->forget('checkout_items');

    return redirect('/online/track')->with('success', 'สั่งซื้อสำเร็จ');
}


    public function generateQRCode(Request $request)
    {
        $amount = number_format($request->query('amount', 0), 2, '.', '');
        $promptPayID = '0843860015'; // เบอร์พร้อมเพย์ร้านคุณ

        // สร้าง URL QR PromptPay
        $url = "https://promptpay.io/{$promptPayID}/{$amount}";

        // Redirect ไปที่ URL นี้เพื่อแสดง QR Code เป็นรูปภาพ
        return redirect($url);
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
}
