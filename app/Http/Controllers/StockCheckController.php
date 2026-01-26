<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductStocks;
use App\Models\ProductStockMovement;
use App\Models\StockCheck;
use App\Models\StockCheckItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockCheckController extends Controller
{
    // เลือกสินค้า
    public function index()
    {
        $products = Product::with(['stocks.unit'])->get();
        return view('staff.stock_check.index', compact('products'));
    }


    // ฟอร์มตรวจนับ
    public function form(Product $product)
    {
        $stocks = ProductStocks::with('unit')
            ->where('product_id', $product->id)
            ->get();

        return view('staff.stock_check.form', compact('product', 'stocks'));
    }

    // บันทึกผลตรวจนับ

    public function store(Request $request)
    {
        DB::transaction(function () use ($request) {

            // 1️⃣ สร้างหัวการตรวจนับ
            $check = StockCheck::create([
                'check_date' => now(),
                'cycle' => now()->format('Y-m'), // ✅ ตัวที่ขาด
                'checked_by' => Auth::id(),
                'remark' => $request->remark ?? null,
            ]);

            // 2️⃣ บันทึกรายการตรวจนับ
            foreach ($request->items as $stockId => $item) {

                $stock = ProductStocks::find($stockId);
                if (!$stock) continue;

                $system = $item['system_qty'];
                $actual = $item['actual_qty'];
                $diff   = $actual - $system;

                StockCheckItem::create([
                    'stock_check_id' => $check->id,
                    'product_id'     => $stock->product_id,
                    'unit_id'        => $stock->unit_id,
                    'system_qty'     => $system,
                    'real_qty'       => $actual,
                    'diff_qty'       => $diff,
                ]);

                // 🔧 ปรับสต็อก (ปรับที่คลัง)
                $stock->update([
                    'warehouse_stock' => $stock->warehouse_stock + $diff
                ]);
            }
        });

        return redirect()
            ->route('staff.stock.check.index')
            ->with('success', 'บันทึกการตรวจนับเรียบร้อย');
    }

    public function report()
    {
        $checks = StockCheck::with('user')->latest()->get();
        return view('staff.stock_check.report', compact('checks'));
    }

    public function detail(StockCheck $check)
{
    $check->load([
        'user',
        'items.product',
        'items.unit',
    ]);

    return view('staff.stock_check.check_detail', compact('check'));
}

}
