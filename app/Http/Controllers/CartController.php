<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product; // ⚠️ ตรวจสอบชื่อ Model สินค้าของคุณให้ถูกต้อง (เช่น Product หรือ Item)

class CartController extends Controller
{
    /**
     * แสดงหน้าตะกร้าสินค้า
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        $total = 0;
        $outOfStockItems = [];

        // ตรวจสอบข้อมูลสต็อกล่าสุดจาก DB และคำนวณยอดรวม
        foreach ($cart as $id => &$item) {
            $product = Product::find($id);

            if ($product) {
                // อัปเดตสต็อกคงเหลือปัจจุบันลง Session
                $item['stock'] = $product->stock;
                
                // คำนวณราคารวม
                $total += $item['price'] * $item['quantity'];

                // ตรวจสอบว่าจำนวนที่เลือกไว้เกินสต็อกที่มีจริงหรือไม่
                if ($item['quantity'] > $product->stock) {
                    $outOfStockItems[] = [
                        'name' => $item['name'],
                        'requested' => $item['quantity'],
                        'available' => $product->stock
                    ];
                }
            }
        }

        // เซฟค่าสต็อกที่อัปเดตกลับเข้า Session
        session()->put('cart', $cart);

        // ส่งข้อมูลไปยัง View (ปรับ path view ให้ตรงตามโฟลเดอร์ของคุณ เช่น online.cart)
        return view('online.cart', compact('cart', 'total', 'outOfStockItems'));
    }

    /**
     * อัปเดตจำนวนสินค้า (รองรับ AJAX PUT)
     */
    public function updateQuantity(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = session()->get('cart', []);

        if (!isset($cart[$id])) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่พบสินค้านี้ในตะกร้า'
            ], 404);
        }

        // ดึงข้อมูลสินค้าจาก Database เพื่อเช็คสต็อกจริง
        $product = Product::find($id);
        $requestedQty = (int) $request->quantity;

        if ($product && $requestedQty > $product->stock) {
            return response()->json([
                'success' => false,
                'message' => "สินค้าคงเหลือเพียง {$product->stock} ชิ้น ไม่พอสำหรับจำนวนที่ระบุ"
            ], 400);
        }

        // อัปเดตจำนวนลงใน Session
        $cart[$id]['quantity'] = $requestedQty;
        if ($product) {
            $cart[$id]['stock'] = $product->stock;
        }
        
        session()->put('cart', $cart);

        // คำนวณยอดรวมใหม่ทั้งหมด
        $itemTotal = $cart[$id]['price'] * $requestedQty;
        $total = 0;
        $totalItems = 0;

        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
            $totalItems += $item['quantity'];
        }

        return response()->json([
            'success' => true,
            'quantity' => $requestedQty,
            'item_total' => number_format($itemTotal, 2),
            'total' => number_format($total, 2),
            'total_items' => $totalItems
        ]);
    }

    /**
     * ลบสินค้าออกจากตะกร้า (รองรับ AJAX DELETE)
     */
    public function remove($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        // คำนวณยอดรวมใหม่หลังลบ
        $total = 0;
        $totalItems = 0;

        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
            $totalItems += $item['quantity'];
        }

        return response()->json([
            'success' => true,
            'total' => number_format($total, 2),
            'total_items' => $totalItems
        ]);
    }
}