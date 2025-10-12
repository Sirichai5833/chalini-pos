<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductStockMovement;
use App\Models\ProductStocks;
use App\Models\ProductUnit;
use Illuminate\Support\Facades\DB;


class StockController extends Controller
{
   public function formMoveToStore()
{
   $rawProducts  = Product::with([
   'productUnits:id,product_id,unit_name,unit_quantity,barcode',
    'stock:id,product_id,warehouse_stock,store_stock', // ✅ ดึง field มาด้วยตรงนี้
])->get();



    // เตรียมข้อมูลแบบรวม store_stock ในแต่ละ unit
   $products = $rawProducts->map(function ($product) {
    $storeStock = optional($product->stock)->store_stock ?? 0;
    $warehouseStock = optional($product->stock)->warehouse_stock ?? 0;

    $product->productUnits = $product->productUnits->map(function ($unit) use ($storeStock, $warehouseStock) {
        $unit->store_stock = $storeStock;
        $unit->warehouse_stock = $warehouseStock;
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

    DB::beginTransaction();
    try {
        $productIds = $request->input('product_id');
        $unitIds = $request->input('unit_id');
        $quantities = $request->input('quantity');

        for ($i = 0; $i < count($productIds); $i++) {
            $productId = $productIds[$i];
            $unitId = $unitIds[$i];
            $quantity = $quantities[$i];

            $mainUnit = ProductUnit::find($unitId);
            if (!$mainUnit) {
                throw new \Exception("ไม่พบหน่วยสินค้ารายการที่ " . ($i + 1));
            }

            $mainStock = ProductStocks::firstOrCreate(
                ['product_id' => $productId, 'unit_id' => $unitId],
                ['warehouse_stock' => 0, 'store_stock' => 0]
            );

            if ($mainStock->warehouse_stock >= $quantity) {
                // ✅ ถ้าสต็อกเพียงพอ → หักแค่หน่วยนั้น
                $mainStock->warehouse_stock -= $quantity;
                $mainStock->store_stock += $quantity;
                $mainStock->save();

                ProductStockMovement::create([
                    'product_id' => $productId,
                    'type' => 'out',
                    'quantity' => $quantity,
                    'unit_quantity' => $mainUnit->unit_quantity,
                    'unit' => $mainUnit->unit_name,
                    'location' => 'warehouse',
                    'note' => 'ย้ายไปหน้าร้าน (หน่วยตรง)',
                ]);

                ProductStockMovement::create([
                    'product_id' => $productId,
                    'type' => 'in',
                    'quantity' => $quantity,
                    'unit_quantity' => $mainUnit->unit_quantity,
                    'unit' => $mainUnit->unit_name,
                    'location' => 'store',
                    'note' => 'รับจากคลัง (หน่วยตรง)',
                ]);

                continue;
            }

            // ❌ ถ้าไม่พอ → ต้องใช้การแปลงจากหน่วยอื่น
            $baseQty = $quantity * $mainUnit->unit_quantity;
            $remainingQty = $baseQty;

            // ค่อยๆ หาและหักจากหน่วยที่ใหญ่กว่าหรือเท่ากัน
            $allUnits = ProductUnit::where('product_id', $productId)
                ->orderByDesc('unit_quantity')
                ->get();

            foreach ($allUnits as $unit) {
                $unitQty = $unit->unit_quantity;
                $convertedQty = floor($remainingQty / $unitQty);
                if ($convertedQty <= 0) continue;

                $stock = ProductStocks::firstOrCreate(
                    ['product_id' => $productId, 'unit_id' => $unit->id],
                    ['warehouse_stock' => 0, 'store_stock' => 0]
                );

                $qtyToUse = min($convertedQty, $stock->warehouse_stock);
                if ($qtyToUse <= 0) continue;

                $stock->warehouse_stock -= $qtyToUse;
                $stock->store_stock += $qtyToUse;
                $stock->save();

                ProductStockMovement::create([
                    'product_id' => $productId,
                    'type' => 'out',
                    'quantity' => $qtyToUse,
                    'unit_quantity' => $unitQty,
                    'unit' => $unit->unit_name,
                    'location' => 'warehouse',
                    'note' => 'ย้ายไปหน้าร้าน (แปลงหน่วย)',
                ]);

                ProductStockMovement::create([
                    'product_id' => $productId,
                    'type' => 'in',
                    'quantity' => $qtyToUse,
                    'unit_quantity' => $unitQty,
                    'unit' => $unit->unit_name,
                    'location' => 'store',
                    'note' => 'รับจากคลัง (แปลงหน่วย)',
                ]);

                $remainingQty -= $qtyToUse * $unitQty;

                if ($remainingQty <= 0) break;
            }

            if ($remainingQty > 0) {
                throw new \Exception("สินค้ารายการที่ " . ($i + 1) . " ไม่สามารถย้ายได้ครบ (ขาด $remainingQty หน่วยย่อย)");
            }
        }

        DB::commit();
        return redirect()->back()->with('success', 'ย้ายสินค้าไปหน้าร้านเรียบร้อยแล้ว');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withErrors(['error' => $e->getMessage()]);
    }
}





public function getProductByBarcode($barcode)
{
    $unit = ProductUnit::with(['product', 'stock'])
        ->where('barcode', $barcode)
        ->first();

    if (!$unit || !$unit->product) {
        return response()->json(['message' => 'ไม่เจอสินค้า'], 404);
    }

    $product = $unit->product;

    
    // โหลด productUnits พร้อม stock ของแต่ละ unit
    $product->load(['productUnits.stock']);

   return response()->json([
    'product_id' => $product->id,
    'product_name' => $product->name,
    'unit_id' => $unit->id,
    'unit_name' => $unit->unit_name,
    'unit_quantity' => $unit->unit_quantity,
    'warehouse_stock' => $unit->stock->warehouse_stock ?? 0,
    'store_stock' => $unit->stock->store_stock ?? 0,
]);

}



}
