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
    // ดึงข้อมูล stock แยกตาม product + unit ที่ stock ต่ำกว่าเกณฑ์
    $stocks = ProductStocks::with(['product', 'unit'])
        ->where(function ($query) {
            $query->where('warehouse_stock', '<', 10)
                  ->orWhere('store_stock', '<', 5);
        })
        ->get();

    $lowStockProducts = $stocks->map(function ($stock) {
        return (object)[
            'product_name' => $stock->product->name,
            'unit_name' => $stock->unit->unit_name ?? '-',
            'warehouse_stock' => $stock->warehouse_stock,
            'store_stock' => $stock->store_stock,
        ];
    });

    return view('notification.OutStock', ['lowStockProducts' => $lowStockProducts]);
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