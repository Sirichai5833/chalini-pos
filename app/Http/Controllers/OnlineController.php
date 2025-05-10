<?php

namespace App\Http\Controllers;
use App\Models\Category; // ← เพิ่มบรรทัดนี้ถ้าคุณใช้ category
use App\Models\Product;
use App\Models\ProductUnit;
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
    $products = Product::with('productUnits')->paginate(10);

    if ($request->filled('category')) {
        $products->where('category_id', $request->category);
    }

    if ($request->filled('search')) {
        $products->where('name', 'like', '%' . $request->search . '%');
    }

    $products = $products->appends($request->all());

    // คำนวณจำนวนสินค้าที่อยู่ในตะกร้า
    $cart = session('cart', []);
    
    // ใช้ collect() และ sum() เพื่อคำนวณจำนวนสินค้าในตะกร้า
    $totalItems = collect($cart)->sum(function ($item) {
        return $item['quantity']; // ตรวจสอบให้แน่ใจว่า `quantity` คือค่าที่คำนวณได้
    });
    return view('online.pos', [
        'categories' => $categories,
        'products' => $products,
        'totalItems' => $totalItems,
    ])->with('totalItems', $totalItems);  // ส่งตัวแปร totalItems ไปยัง view
    
}




// Controller ที่รับข้อมูลจากฟอร์ม
// app/Http/Controllers/CartController.php

// app/Http/Controllers/CartController.php

// app/Http/Controllers/CartController.php

// app/Http/Controllers/CartController.php

public function add(Request $request)
{
    $productId = $request->input('product_id');
    $cart = session()->get('cart', []);

    // เพิ่มสินค้าลงในตะกร้า
    if (isset($cart[$productId])) {
        $cart[$productId]['quantity']++;
    } else {
        // เพิ่มข้อมูลสินค้าใหม่
        $product = Product::findOrFail($productId);
        $productUnit = $product->productUnits->first();

        $cart[$productId] = [
            'name' => $product->name,
            'price' => $productUnit->price,
            'quantity' => 1,
            'image' => $product->image,
        ];
    }

    session()->put('cart', $cart);

    // คำนวณจำนวนสินค้าทั้งหมดในตะกร้า
    $totalItems = collect($cart)->sum('quantity');

    // ส่งกลับจำนวนสินค้าทั้งหมดที่อัพเดต
    return response()->json(['success' => true, 'totalItems' => $totalItems]);
}



}
