<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');

        // =========================
        // 1) SALE (หน้าร้าน)
        // =========================
        $query = Sale::with('staff')->orderBy('sale_date', 'desc');

        if ($startDate && $endDate) {
            $query->whereBetween('sale_date', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);
        }

        $sales = $query->get();

        // =========================
        // 2) ORDER (ออนไลน์)
        // =========================
        $onlineOrdersQuery = Order::where('status', 'เสร็จสิ้น');

        if ($startDate && $endDate) {
            $onlineOrdersQuery->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);
        }

        $onlineOrders = $onlineOrdersQuery->get();

        // =========================
        // 3) CHART
        // =========================
        $allSalesForChart = collect();

        foreach ($sales as $sale) {
            $allSalesForChart->push([
                'datetime' => $sale->sale_date,
                'total' => $sale->total_price,
            ]);
        }

        foreach ($onlineOrders as $order) {
            $allSalesForChart->push([
                'datetime' => $order->created_at,
                'total' => $order->total_amount,
            ]);
        }

        $groupedChartData = $allSalesForChart->groupBy(function ($item) use ($startDate, $endDate) {

            if (!$startDate || !$endDate) {
                return Carbon::parse($item['datetime'])->format('d M');
            }

            $diff = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate));

            if ($diff <= 1) {
                return Carbon::parse($item['datetime'])->format('H:00');
            } elseif ($diff <= 31) {
                return Carbon::parse($item['datetime'])->format('d M');
            } else {
                return Carbon::parse($item['datetime'])->format('M Y');
            }
        });

        $chartLabels = $groupedChartData->keys();
        $chartData = $groupedChartData->map(fn($items) => collect($items)->sum('total'))->values();

        // =========================
        // 4) TOTAL SALES
        // =========================
        $totalSales = $sales->sum('total_price') + $onlineOrders->sum('total_amount');
        $onlineSalesTotal = $onlineOrders->sum('total_amount');
        
        // =========================
        // 5) CATEGORY
        // =========================
        $categoryId = $request->input('category_id');

        // =========================
        // 6) TOP PRODUCTS (หน้าร้าน)
        // =========================
        $topProducts = DB::table('sale_items')
            ->join('product_units', 'sale_items.product_unit_id', '=', 'product_units.id')
            ->join('products', 'product_units.product_id', '=', 'products.id')
            ->when($categoryId, fn($q) => $q->where('products.category_id', $categoryId))
            ->select(
                'products.name',
                DB::raw('SUM(sale_items.quantity) as total_quantity'),
                DB::raw('SUM(sale_items.quantity * sale_items.price) as total_sales')
            )
            ->whereIn('sale_items.sale_id', $sales->pluck('id'))
            ->groupBy('products.name')
            ->orderByDesc('total_quantity')
            ->get();

        // =========================
        // 7) TOP PRODUCTS (ออนไลน์)
        // =========================
        $topOnlineProducts = DB::table('order_items')
            ->join('product_units', 'order_items.product_unit_id', '=', 'product_units.id')
            ->join('products', 'product_units.product_id', '=', 'products.id')
            ->when($categoryId, fn($q) => $q->where('products.category_id', $categoryId))
            ->select(
                'products.name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.quantity * order_items.price) as total_sales')
            )
            ->whereIn('order_items.order_id', $onlineOrders->pluck('id'))
            ->groupBy('products.name')
            ->orderByDesc('total_quantity')
            ->get();

        // =========================
        // 8) รวม TOP PRODUCTS
        // =========================
        $topAllProducts = collect()
            ->merge($topProducts)
            ->merge($topOnlineProducts)
            ->groupBy('name')
            ->map(function ($items, $name) {
                return [
                    'name' => $name,
                    'total_quantity' => $items->sum('total_quantity'),
                    'total_sales' => $items->sum('total_sales'),
                ];
            })
            ->values()
            ->sortByDesc('total_quantity')
            ->take(10);

        // ======================================================
        // 🔥 9) COST + PROFIT (แก้ไขใหม่: ดึงต้นทุนที่ล็อกไว้มาคำนวณ)
        // ======================================================
        // คำนวณต้นทุนรวมจากประวัติการขายหน้าร้าน
        $offlineCost = DB::table('sale_items')
            ->whereIn('sale_id', $sales->pluck('id'))
            ->sum(DB::raw('quantity * cost'));

        // คำนวณต้นทุนรวมจากประวัติออเดอร์ออนไลน์
        $onlineCost = DB::table('order_items')
            ->whereIn('order_id', $onlineOrders->pluck('id'))
            ->sum(DB::raw('quantity * cost'));

        // รวมต้นทุนทั้งหมด
        $totalCost = $offlineCost + $onlineCost;

        // คำนวณกำไรสุทธิ
        $netProfit = $totalSales - $totalCost;

        $categories = DB::table('categories')->get();
        
        // =========================
        return view('reports.daily', compact(
            'sales',
            'totalSales',
            'onlineSalesTotal',
            'chartLabels',
            'chartData',
            'totalCost',
            'netProfit',
            'onlineOrders',
            'topProducts',
            'topOnlineProducts',
            'topAllProducts',
            'startDate',
            'endDate',
            'categories' 
        ));
    }
}