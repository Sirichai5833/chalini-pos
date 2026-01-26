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
            foreach ($request->product_id as $i => $productId) {

                $unitId   = $request->unit_id[$i];
                $needQty  = $request->quantity[$i];

                $unit = ProductUnit::findOrFail($unitId);

                // stock หน่วยที่ user เลือก
                $stock = ProductStocks::firstOrCreate(
                    ['product_id' => $productId, 'unit_id' => $unitId],
                    ['warehouse_stock' => 0, 'store_stock' => 0]
                );

                // ✅ ถ้าหน่วยที่เลือกมีพอ → ใช้เลย
                if ($stock->warehouse_stock < $needQty) {

                    $biggerUnits = ProductUnit::where('product_id', $productId)
                        ->where('unit_quantity', '>', $unit->unit_quantity)
                        ->orderBy('unit_quantity') // เล็ก → ใหญ่
                        ->get();

                    foreach ($biggerUnits as $bigUnit) {

                        $bigStock = ProductStocks::where('product_id', $productId)
                            ->where('unit_id', $bigUnit->id)
                            ->first();

                        if (!$bigStock || $bigStock->warehouse_stock <= 0) {
                            continue;
                        }

                        $converted = $bigUnit->unit_quantity / $unit->unit_quantity;

                        while (
                            $stock->warehouse_stock < $needQty &&
                            $bigStock->warehouse_stock > 0
                        ) {

                            // แตก 1 หน่วยใหญ่
                            $bigStock->decrement('warehouse_stock', 1);
                            $stock->increment('warehouse_stock', $converted);

                            ProductStockMovement::create([
                                'product_id' => $productId,
                                'type' => 'out',
                                'quantity' => 1,
                                'unit' => $bigUnit->unit_name,
                                'unit_quantity' => $bigUnit->unit_quantity,
                                'location' => 'warehouse',
                                'note' => 'แตกหน่วย',
                            ]);

                            ProductStockMovement::create([
                                'product_id' => $productId,
                                'type' => 'in',
                                'quantity' => $converted,
                                'unit' => $unit->unit_name,
                                'unit_quantity' => $unit->unit_quantity,
                                'location' => 'warehouse',
                                'note' => 'รับจากการแตกหน่วย',
                            ]);
                        }

                        if ($stock->warehouse_stock >= $needQty) {
                            break;
                        }
                    }

                    if ($stock->warehouse_stock < $needQty) {
                        throw new \Exception('สต็อกไม่พอแม้หลังจากแตกหน่วย');
                    }
                }



                // ✅ ย้ายไปหน้าร้าน
                $stock->decrement('warehouse_stock', $needQty);
                $stock->increment('store_stock', $needQty);

                ProductStockMovement::create([
                    'product_id' => $productId,
                    'type' => 'out',
                    'quantity' => $needQty,
                    'unit' => $unit->unit_name,
                    'unit_quantity' => $unit->unit_quantity,
                    'location' => 'warehouse',
                    'note' => 'ย้ายไปหน้าร้าน',
                ]);

                ProductStockMovement::create([
                    'product_id' => $productId,
                    'type' => 'in',
                    'quantity' => $needQty,
                    'unit' => $unit->unit_name,
                    'unit_quantity' => $unit->unit_quantity,
                    'location' => 'store',
                    'note' => 'รับเข้าหน้าร้าน',
                ]);
            }

            DB::commit();
            return back()->with('success', 'ย้ายสินค้าเรียบร้อยแล้ว');
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

