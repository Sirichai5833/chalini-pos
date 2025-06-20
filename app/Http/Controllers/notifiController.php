<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductBatch;
use Illuminate\Http\Request;

class NotifiController extends Controller
{
    // แสดงสินค้าคงคลังใกล้หมด
    public function lowStock()
{
    // ดึงเฉพาะสินค้าใกล้หมด (ไม่ต้องแสดงทุกหน่วย)
    $products = Product::with('stock')
        ->whereHas('stock', function ($query) {
            $query->where('warehouse_stock', '<', 10)
                  ->orWhere('store_stock', '<', 5);
        })
        ->get()
        ->map(function ($product) {
            return (object)[
                'name' => $product->name,
                'warehouse_stock' => $product->stock->warehouse_stock ?? 0,
                'store_stock' => $product->stock->store_stock ?? 0,
            ];
        });

    return view('notification.OutStock', ['lowStockProducts' => $products]);
}



    // แสดงสินค้าใกล้หมดอายุ
   public function nearExpiry()
{
    $nearExpiryProducts = ProductBatch::with('product')
        ->where('is_acknowledged', false) // ✅ เพิ่มบรรทัดนี้
        ->whereDate('expiry_date', '<=', now()->addDays(30))
        ->orderBy('expiry_date')
        ->get()
        ->map(function ($batch) {
            return (object)[
                'id' => $batch->id,
                'name' => $batch->product->name,
                'quantity' => $batch->quantity,
                'expiry_date' => $batch->expiry_date,
                'batch_code' => $batch->batch_code,
            ];
        });

    return view('notification.expire', compact('nearExpiryProducts'));
}


public function acknowledgeExpiry($id)
{
    $batch = ProductBatch::findOrFail($id);
    $batch->is_acknowledged = true;
    $batch->save();

    return redirect()->back()->with('success', 'รับทราบและจัดการสินค้าเรียบร้อยแล้ว');
}
}

?>