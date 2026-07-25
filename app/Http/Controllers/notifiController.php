<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\ProductStocks;
use Illuminate\Http\Request;

class NotifiController extends Controller
{
    // แสดงสินค้าคงคลังใกล้หมด
public function lowStock()
{
    $stocks = ProductStocks::with(['product', 'unit'])->get();

    $lowStockProducts = $stocks
        ->groupBy('product_id') // 🔥 รวมตามสินค้า
        ->map(function ($items) {

            $product = $items->first()->product;
            $minStock = $product->min_stock ?? 0;

            // ✅ รวมทุกหน่วย → แปลงเป็นหน่วยเล็กสุด
            $totalStock = $items->sum(function ($item) {
                $unitQty = $item->unit_quantity ?? 1;

                $stock = ($item->warehouse_stock ?? 0) + ($item->store_stock ?? 0);

                return $stock * $unitQty;
            });

            return (object)[
                'product_name' => $product->name,
                'total_stock' => $totalStock,
                'min_stock' => $minStock,
            ];
        })
        ->filter(function ($item) {
            return $item->total_stock <= $item->min_stock;
        })
        ->values(); // reset index

    return view('notification.OutStock', [
        'lowStockProducts' => $lowStockProducts
    ]);
}


    // แสดงสินค้าใกล้หมดอายุ
  public function nearExpiry()
{
    $nearExpiryProducts = ProductBatch::with(['product', 'productUnit'])
        ->where('is_acknowledged', false)
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
                'unit_name' => $batch->productUnit->unit_name ?? '-',
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