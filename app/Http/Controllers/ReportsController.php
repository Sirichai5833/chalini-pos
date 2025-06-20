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

        $saleCost = DB::table('sale_items')
            ->join('product_units', 'sale_items.product_unit_id', '=', 'product_units.id')
            ->whereIn('sale_items.sale_id', $sales->pluck('id'))
            ->selectRaw('SUM(sale_items.quantity * product_units.cost_price) as total_cost')
            ->value('total_cost');

        $onlineOrderCost = DB::table('order_items')
            ->join('product_units', 'order_items.product_unit_id', '=', 'product_units.id')
            ->whereIn('order_items.order_id', $onlineOrders->pluck('id'))
            ->selectRaw('SUM(order_items.quantity * product_units.cost_price) as total_cost')
            ->value('total_cost');

        $totalCost = $saleCost + $onlineOrderCost;
        $netProfit = $totalSales - $totalCost;

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
            'topOnlineProducts' 
        ));
    }
}
