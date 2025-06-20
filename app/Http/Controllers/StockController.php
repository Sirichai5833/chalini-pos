<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductStockMovement;
use App\Models\ProductStocks;
use App\Models\ProductUnit;

class StockController extends Controller
{
   public function formMoveToStore()
{
   $products = Product::with([
    'productUnits:id,product_id,unit_name,unit_quantity',
    'stock:id,product_id,warehouse_stock,store_stock', // ✅ ดึง field มาด้วยตรงนี้
])->get();



    // เตรียมข้อมูลแบบรวม store_stock ในแต่ละ unit
    $products = $products->map(function ($product) {
        $storeStock = optional($product->stock)->store_stock ?? 0;

        // ใส่ store_stock เข้าไปในแต่ละ unit
       $product->productUnits = $product->productUnits->map(function ($unit) use ($product) {
    $unit->store_stock = optional($product->stock)->store_stock ?? 0;
    $unit->warehouse_stock = optional($product->stock)->warehouse_stock ?? 0; // ✅ ดึงจาก stock ได้เลย
    return $unit;
});

        return $product;
    });

    return view('products.move-to-store', compact('products'));
}



    public function moveToStore(Request $request)
{
    $request->validate([
        'product_id' => 'required|array',
        'product_id.*' => 'required|exists:products,id',
        'unit_id' => 'required|array',
        'unit_id.*' => 'required|exists:product_units,id',
        'quantity' => 'required|array',
        'quantity.*' => 'required|integer|min:1',
    ]);

    $productIds = $request->input('product_id');
    $unitIds = $request->input('unit_id');
    $quantities = $request->input('quantity');

    for ($i = 0; $i < count($productIds); $i++) {
        $productId = $productIds[$i];
        $unitId = $unitIds[$i];
        $quantity = $quantities[$i];

        $productStock = ProductStocks::where('product_id', $productId)->first();
        $productUnit = ProductUnit::findOrFail($unitId);

        $totalQty = $quantity * $productUnit->unit_quantity;

        // ตรวจสอบว่าสต็อกคลังพอ
        if ($productStock->warehouse_stock < $totalQty) {
            return back()->withErrors([
                "warehouse_stock_$i" => "สินค้ารายการที่ " . ($i + 1) . " สต็อกคลังไม่พอ"
            ]);
        }

        // อัปเดตสต็อก
        $productStock->warehouse_stock -= $totalQty;
        $productStock->store_stock += $totalQty;
        $productStock->save();

        // บันทึกประวัติคลัง -> หน้าร้าน
        ProductStockMovement::create([
            'product_id' => $productId,
            'type' => 'out',
            'quantity' => $quantity,
            'unit_quantity' => $productUnit->unit_quantity,
            'unit' => $productUnit->unit_name,
            'location' => 'warehouse',
            'note' => 'ย้ายไปหน้าร้าน',
        ]);

        ProductStockMovement::create([
            'product_id' => $productId,
            'type' => 'in',
            'quantity' => $quantity,
            'unit_quantity' => $productUnit->unit_quantity,
            'unit' => $productUnit->unit_name,
            'location' => 'store',
            'note' => 'รับจากคลัง',
        ]);
    }

    return redirect()->back()->with('success', 'ย้ายสินค้าไปหน้าร้านเรียบร้อยแล้ว');
}

public function getProductByBarcode($barcode)
{
    // ค้นหา barcode จาก product_units
    $unit = ProductUnit::with('product') // โหลด product ที่เกี่ยวข้องมาด้วย
        ->where('barcode', $barcode)
        ->first();

    if (!$unit || !$unit->product) {
        return response()->json(['message' => 'ไม่เจอสินค้า'], 404);
    }

    // ดึง stock ของ product
    $product = $unit->product->load(['stock']);

    // เติม stock เข้าไปในแต่ละ unit
    $product->productUnits = $product->productUnits->map(function ($unitItem) use ($product) {
        $unitItem->store_stock = optional($product->stock)->store_stock ?? 0;
        $unitItem->warehouse_stock = optional($product->stock)->warehouse_stock ?? 0;
        return $unitItem;
    });

    return response()->json([
        'product' => $product,
        'selected_unit' => $unit // unit ที่ได้จาก barcode
    ]);
}


}
