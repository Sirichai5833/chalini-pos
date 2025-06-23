<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Sale;
use App\Models\User; // เพิ่มบรรทัดนี้
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash; // เพิ่มบรรทัดนี้
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;

class ReportsController extends Controller
{
    public function index(Request $request)
    {


        // รับค่า filter จาก GET request
        $reportType = $request->input('report_type', 'daily');  // daily, monthly, yearly
        $reportDate = $request->input('report_date', date('Y-m-d'));

        // เริ่ม query รายการขาย พร้อม join พนักงาน
        $query = Sale::with('staff')->orderBy('sale_date', 'desc');


        // ดึงคำสั่งซื้อออนไลน์ที่ 'เสร็จสิ้น' ตามช่วงเวลาเดียวกัน
        $onlineOrders = Order::where('status', 'เสร็จสิ้น');

        if ($reportType === 'daily') {
            $onlineOrders->whereDate('created_at', $reportDate);
        } elseif ($reportType === 'monthly') {
            $year = date('Y', strtotime($reportDate));
            $month = date('m', strtotime($reportDate));
            $onlineOrders->whereYear('created_at', $year)->whereMonth('created_at', $month);
        } elseif ($reportType === 'yearly') {
            $year = date('Y', strtotime($reportDate));
            $onlineOrders->whereYear('created_at', $year);
        }

        $onlineOrders = $onlineOrders->get();

        // กรองตามประเภทช่วงเวลา
        if ($reportType === 'daily') {
            $query->whereDate('sale_date', $reportDate);
        } elseif ($reportType === 'monthly') {
            $year = date('Y', strtotime($reportDate));
            $month = date('m', strtotime($reportDate));
            $query->whereYear('sale_date', $year)->whereMonth('sale_date', $month);
        } elseif ($reportType === 'yearly') {
            $year = date('Y', strtotime($reportDate));
            $query->whereYear('sale_date', $year);
        }

        $sales = $query->get();
        // จัดกลุ่มยอดขายสำหรับกราฟ
        $allSalesForChart = collect();

        // รวมรายการขายหน้าร้าน
        foreach ($sales as $sale) {
            $allSalesForChart->push([
                'datetime' => $sale->sale_date,
                'total' => $sale->total_price,
            ]);
        }

        // รวมรายการสั่งซื้อออนไลน์
        foreach ($onlineOrders as $order) {
            $allSalesForChart->push([
                'datetime' => $order->created_at,
                'total' => $order->total_amount,
            ]);
        }

        // จัดกลุ่มข้อมูลตามช่วงเวลา
        $groupedChartData = $allSalesForChart->groupBy(function ($item) use ($reportType) {
            return match ($reportType) {
                'daily' => \Carbon\Carbon::parse($item['datetime'])->format('H:00'),
                'monthly' => \Carbon\Carbon::parse($item['datetime'])->format('d M'),
                'yearly' => \Carbon\Carbon::parse($item['datetime'])->format('M Y'),
                default => 'N/A',
            };
        });

        $chartLabels = $groupedChartData->keys();
        $chartData = $groupedChartData->map(function ($items) {
            return collect($items)->sum('total');
        })->values();

        // รวมยอดขายทั้งหมด
        $totalSales = $sales->sum('total_price');
        $onlineSalesTotal = $onlineOrders->sum('total_amount');
        $totalSales += $onlineSalesTotal;


        // ดึงข้อมูลสินค้าขายดี (sum quantity และยอดขาย) จาก sale_items ที่อยู่ในช่วง sales นี้
        $topProducts = DB::table('sale_items')
            ->join('product_units', 'sale_items.product_unit_id', '=', 'product_units.id')
            ->join('products', 'product_units.product_id', '=', 'products.id')
            ->select(
                'products.name',
                DB::raw('SUM(sale_items.quantity) as total_quantity'),
                DB::raw('SUM(sale_items.quantity * sale_items.price) as total_sales')
            )
            ->whereIn('sale_items.sale_id', $sales->pluck('id'))
            ->groupBy('products.name')
            ->orderByDesc('total_quantity')
            ->get();


        $freeItems = DB::table('sale_items')
    ->join('product_units', 'sale_items.product_unit_id', '=', 'product_units.id')
    ->join('products', 'product_units.product_id', '=', 'products.id')
    ->select('products.name', 'sale_items.quantity')
    ->whereIn('sale_items.sale_id', $sales->pluck('id'))
    ->get();

        $topOnlineProducts = DB::table('order_items')
    ->join('product_units', 'order_items.product_unit_id', '=', 'product_units.id')
    ->join('products', 'product_units.product_id', '=', 'products.id')
    ->select(
        'products.name',
        DB::raw('SUM(order_items.quantity) as total_quantity'),
        DB::raw('SUM(order_items.quantity * order_items.price) as total_sales')
    )
    ->whereIn('order_items.order_id', $onlineOrders->pluck('id'))
    ->groupBy('products.name')
    ->orderByDesc('total_quantity')
    ->get();

    // ดึงยอดขายจาก sale_items แยกตาม product_unit_id
$saleItemsQty = DB::table('sale_items')
    ->select('product_unit_id', DB::raw('SUM(quantity) as total_qty'))
    ->whereIn('sale_id', $sales->pluck('id'))
    ->groupBy('product_unit_id')
    ->get()
    ->mapWithKeys(function ($item) {
        return [$item->product_unit_id => $item->total_qty];
    });


// ดึงยอดขายออนไลน์จาก order_items แยกตาม product_unit_id
$onlineItemsQty = DB::table('order_items')
    ->select('product_unit_id', DB::raw('SUM(quantity) as total_qty'))
    ->whereIn('order_id', $onlineOrders->pluck('id'))
    ->groupBy('product_unit_id')
    ->pluck('total_qty', 'product_unit_id');

// รวมยอดขายทั้งหมดของแต่ละ product_unit_id
// รวมยอดขายทั้งหมดของแต่ละ product_unit_id โดยรวมค่าจากทั้งสอง Collection ที่มีคีย์เป็น product_unit_id
$totalItemsQty = $saleItemsQty->union($onlineItemsQty)->map(function ($qty, $productUnitId) use ($saleItemsQty, $onlineItemsQty) {
    $saleQty = $saleItemsQty->get($productUnitId, 0);
    $onlineQty = $onlineItemsQty->get($productUnitId, 0);
    return $saleQty + $onlineQty;
});


// ดึงข้อมูล mapping ของ product_unit_id => product_id
$productUnitToProduct = DB::table('product_units')
    ->pluck('product_id', 'id'); // [product_unit_id => product_id]

// ดึงยอดของแถมแยกตาม product_id
$freeByProductId = DB::table('product_stock_movements')
    ->select('product_id', DB::raw('SUM(quantity) as free_qty'))
    ->where('is_free', 1)
    ->groupBy('product_id')
    ->pluck('free_qty', 'product_id');

// ดึงต้นทุนของแต่ละ product_unit_id
$costPrices = DB::table('product_units')
    ->pluck('cost_price', 'id');

// รวมต้นทุน
$totalCost = 0;
foreach ($totalItemsQty as $productUnitId => $qty) {
    if (!isset($productUnitToProduct[$productUnitId])) {
        continue;
    }
    $productId = $productUnitToProduct[$productUnitId];
    $freeQty = $freeByProductId->get($productId, 0);
    $actualQty = max($qty - $freeQty, 0);
    $costPrice = $costPrices->get($productUnitId, 0);


    $totalCost += $actualQty * $costPrice;
}

$netProfit = $totalSales - $totalCost; // ✅ เพิ่มตรงนี้




        // ส่งข้อมูลไปยัง view
        return view('reports.daily', compact(
            'sales',
            'totalSales',
            'topProducts',
            'chartLabels',
            'chartData',
            'totalCost',
            'netProfit',
            'onlineSalesTotal',
            'onlineOrders',
            'topOnlineProducts',
        ));
    }
}
