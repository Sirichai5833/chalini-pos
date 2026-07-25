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
            'cycle' => now()->format('Y-m'),
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

            // ✅ ดึง note
            $note = $item['note'] ?? null;

            StockCheckItem::create([
                'stock_check_id' => $check->id,
                'product_id'     => $stock->product_id,
                'unit_id'        => $stock->unit_id,
                'system_qty'     => $system,
                'real_qty'       => $actual,
                'diff_qty'       => $diff,
            ]);

            // 🔧 ปรับสต็อก
            // 🔧 ปรับสต็อก

// กรณีสินค้าเกิน
if ($diff > 0) {

    $stock->warehouse_stock += $diff;
}

// กรณีสินค้าขาด
elseif ($diff < 0) {

    $needToReduce = abs($diff);

    // หักคลังก่อน
    if ($stock->warehouse_stock >= $needToReduce) {

        $stock->warehouse_stock -= $needToReduce;

    } else {

        // คลังไม่พอ
        $remaining = $needToReduce - $stock->warehouse_stock;

        // เคลียร์คลังให้เหลือ 0
        $stock->warehouse_stock = 0;

        // หักหน้าร้านต่อ
        $stock->store_stock -= $remaining;

        // กันค่าติดลบ
        if ($stock->store_stock < 0) {
            $stock->store_stock = 0;
        }
    }
}

$stock->save();
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
