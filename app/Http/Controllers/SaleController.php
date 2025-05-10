<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log; // ต้องเพิ่มบรรทัดนี้
use App\Models\Product;
use App\Models\ProductStocks;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    // แสดงหน้าขาย (POS)
    public function index()
    {
        $products = Product::with('productUnits')->get();

        $productData = [];
        foreach ($products as $product) {
            foreach ($product->productUnits as $unit) {
                $productData[] = [
                    'id' => $product->id,
                    'barcode' => $unit->barcode,
                    'name' => $product->name,
                    'unit' => $unit->unit_name,
                    'retail_price' => $unit->price, // ใช้ price เดิมเป็นราคาปลีก
                    'wholesale_price' => $unit->wholesale, // หรือจะใช้ logic แยกราคาได้ตามจริง
                    'freebie' => '-',
                    'cost_price' => $unit->unit_quantity,
                ];
            }
        }

        return view('sale.sale', ['products' => $productData]);
    }

    // อัปเดตสต็อกหลังขาย
    public function updateStockAfterSale(Request $request)
    {
        $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.qty' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            foreach ($request->products as $item) {
                $stock = ProductStocks::where('product_id', $item['id'])->first();

                if (!$stock) {
                    return response()->json([
                        'success' => false,
                        'message' => 'ไม่พบข้อมูลสต็อกของสินค้า ID: ' . $item['id']
                    ], 404);
                }

                if ($stock->store_stock < $item['qty']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'สินค้าคงเหลือไม่พอสำหรับสินค้า ID: ' . $item['id']
                    ], 400);
                }

                // ตัดสต็อก
                $stock->store_stock -= $item['qty'];
                $stock->save();

                // (Optional) เพิ่มประวัติการเคลื่อนไหวสต็อก
                // $stock->movements()->create([...]);
            }

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Stock update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการอัปเดตสต็อก',
                'error' => $e->getMessage() // สำหรับ debug
            ], 500);
        }
    }

    public function history()
    {
        $sales = Sale::with(['items.product', 'items.unit', 'staff'])->orderBy('sale_date', 'desc')->get();

        return view('sale.history', compact('sales'));
    }
}
