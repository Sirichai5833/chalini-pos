<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
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
    
    

    public function updateQuantity(Request $request, $productId)
    {
        $cart = session()->get('cart', []);
    
        if (isset($cart[$productId])) {
            if ($request->input('action') === 'increase') {
                $cart[$productId]['quantity']++;
            } elseif ($request->input('action') === 'decrease' && $cart[$productId]['quantity'] > 1) {
                $cart[$productId]['quantity']--;
            } else {
                // กรณีป้อนจำนวนที่ไม่ถูกต้อง
                $cart[$productId]['quantity'] = max(1, $cart[$productId]['quantity']);
            }
        }
    
        session()->put('cart', $cart);
    
        return redirect()->route('online.cart')->with('success', 'อัปเดตจำนวนสินค้าในตะกร้าแล้ว!');
    }
    

    public function index()
    {
        $cart = session('cart', []);
        $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);

        return view('online.cart', compact('cart', 'total'));
    }
}
