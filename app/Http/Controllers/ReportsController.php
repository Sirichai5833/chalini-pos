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
        /*
        |--------------------------------------------------------------------------
        | 1. รับช่วงวันที่
        |--------------------------------------------------------------------------
        */

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $categoryId = $request->input('category_id');

        /*
        |--------------------------------------------------------------------------
        | 2. รายการขายหน้าร้าน
        |--------------------------------------------------------------------------
        */

        $salesQuery = Sale::with('staff')
            ->orderByDesc('sale_date');

        if ($startDate && $endDate) {
            $salesQuery->whereBetween('sale_date', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ]);
        }

        /*
         * เก็บรายการทั้งหมดไว้ก่อน เพื่อให้ View สามารถแสดง
         * ยอดรายการยกเลิกได้
         */
        $allSales = $salesQuery->get();

        /*
        |--------------------------------------------------------------------------
        | 3. กรองรายการขายหน้าร้านที่ถูกยกเลิก
        |--------------------------------------------------------------------------
        */

        $cancelledStatuses = [
            'cancelled',
            'canceled',
            'void',
            'ยกเลิก',
            'ถูกยกเลิก',
        ];

        $isCancelled = static function ($record) use ($cancelledStatuses): bool {
            $status = mb_strtolower(
                trim((string) ($record->status ?? ''))
            );

            return in_array($status, $cancelledStatuses, true)
                || !empty($record->cancelled_at)
                || !empty($record->canceled_at)
                || (bool) ($record->is_cancelled ?? false);
        };

        /*
         * $validSales ใช้คำนวณรายงานทั้งหมด
         * โดยไม่รวมรายการยกเลิก
         */
        $validSales = $allSales
            ->reject($isCancelled)
            ->values();

        /*
         * ส่งรายการทั้งหมดให้ View เพื่อให้ View ตรวจแยก
         * รายการปกติและรายการยกเลิกได้
         */
        $sales = $allSales;

        /*
        |--------------------------------------------------------------------------
        | 4. รายการสั่งซื้อออนไลน์
        |--------------------------------------------------------------------------
        */

        $onlineOrdersQuery = Order::with('user')
            ->where('status', 'เสร็จสิ้น')
            ->orderByDesc('created_at');

        if ($startDate && $endDate) {
            $onlineOrdersQuery->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ]);
        }

        /*
         * เนื่องจากเลือกเฉพาะสถานะ "เสร็จสิ้น"
         * จึงไม่รวมออเดอร์ที่ถูกยกเลิกอยู่แล้ว
         */
        $onlineOrders = $onlineOrdersQuery->get();

        /*
        |--------------------------------------------------------------------------
        | 5. ยอดขายสุทธิ
        |--------------------------------------------------------------------------
        */

        $offlineSalesTotalNet = (float) $validSales->sum('total_price');
        $onlineSalesTotalNet = (float) $onlineOrders->sum('total_amount');

        $totalSalesNet = $offlineSalesTotalNet + $onlineSalesTotalNet;

        /*
         * เก็บชื่อตัวแปรเดิมไว้ เผื่อส่วนอื่นของระบบยังใช้งานอยู่
         */
        $totalSales = $totalSalesNet;
        $onlineSalesTotal = $onlineSalesTotalNet;

        /*
        |--------------------------------------------------------------------------
        | 6. ข้อมูลกราฟยอดขาย
        |--------------------------------------------------------------------------
        */

        $allSalesForChart = collect();

        foreach ($validSales as $sale) {
            $allSalesForChart->push([
                'datetime' => $sale->sale_date,
                'total' => (float) $sale->total_price,
            ]);
        }

        foreach ($onlineOrders as $order) {
            $allSalesForChart->push([
                'datetime' => $order->created_at,
                'total' => (float) $order->total_amount,
            ]);
        }

        $groupedChartData = $allSalesForChart
            ->groupBy(function ($item) use ($startDate, $endDate) {
                $date = Carbon::parse($item['datetime']);

                if (!$startDate || !$endDate) {
                    return $date->format('d M');
                }

                $diffInDays = Carbon::parse($startDate)
                    ->diffInDays(Carbon::parse($endDate));

                if ($diffInDays <= 1) {
                    return $date->format('H:00');
                }

                if ($diffInDays <= 31) {
                    return $date->format('d M');
                }

                return $date->format('M Y');
            });

        /*
         * ตัวแปรชื่อ Net ที่ View ต้องการ
         */
        $chartLabelsNet = $groupedChartData
            ->keys()
            ->values()
            ->all();

        $chartDataNet = $groupedChartData
            ->map(function ($items) {
                return (float) collect($items)->sum('total');
            })
            ->values()
            ->all();

        /*
         * เก็บชื่อตัวแปรเดิมไว้ เผื่อมีโค้ดส่วนอื่นเรียกใช้
         */
        $chartLabels = collect($chartLabelsNet);
        $chartData = collect($chartDataNet);

        /*
        |--------------------------------------------------------------------------
        | 7. สินค้าขายดีหน้าร้าน
        |--------------------------------------------------------------------------
        */

        $validSaleIds = $validSales
            ->pluck('id')
            ->filter()
            ->values();

        $topProducts = DB::table('sale_items')
            ->join(
                'product_units',
                'sale_items.product_unit_id',
                '=',
                'product_units.id'
            )
            ->join(
                'products',
                'product_units.product_id',
                '=',
                'products.id'
            )
            ->when(
                $categoryId,
                fn ($query) => $query->where(
                    'products.category_id',
                    $categoryId
                )
            )
            ->whereIn('sale_items.sale_id', $validSaleIds)
            ->select(
                'products.name',
                DB::raw(
                    'SUM(sale_items.quantity) AS total_quantity'
                ),
                DB::raw(
                    'SUM(sale_items.quantity * sale_items.price) AS total_sales'
                )
            )
            ->groupBy('products.name')
            ->orderByDesc('total_quantity')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 8. สินค้าขายดีออนไลน์
        |--------------------------------------------------------------------------
        */

        $validOnlineOrderIds = $onlineOrders
            ->pluck('id')
            ->filter()
            ->values();

        $topOnlineProducts = DB::table('order_items')
            ->join(
                'product_units',
                'order_items.product_unit_id',
                '=',
                'product_units.id'
            )
            ->join(
                'products',
                'product_units.product_id',
                '=',
                'products.id'
            )
            ->when(
                $categoryId,
                fn ($query) => $query->where(
                    'products.category_id',
                    $categoryId
                )
            )
            ->whereIn('order_items.order_id', $validOnlineOrderIds)
            ->select(
                'products.name',
                DB::raw(
                    'SUM(order_items.quantity) AS total_quantity'
                ),
                DB::raw(
                    'SUM(order_items.quantity * order_items.price) AS total_sales'
                )
            )
            ->groupBy('products.name')
            ->orderByDesc('total_quantity')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 9. รวมสินค้าขายดีหน้าร้านและออนไลน์
        |--------------------------------------------------------------------------
        */

        $topAllProductsNet = collect()
            ->merge($topProducts)
            ->merge($topOnlineProducts)
            ->groupBy('name')
            ->map(function ($items, $name) {
                return [
                    'name' => $name,

                    'total_quantity' => (float) $items->sum(
                        'total_quantity'
                    ),

                    'total_sales' => (float) $items->sum(
                        'total_sales'
                    ),
                ];
            })
            ->values()
            ->sortByDesc('total_quantity')
            ->take(10)
            ->values();

        /*
         * เก็บชื่อตัวแปรเดิมไว้
         */
        $topAllProducts = $topAllProductsNet;

        /*
        |--------------------------------------------------------------------------
        | 10. ต้นทุนหน้าร้าน
        |--------------------------------------------------------------------------
        */

        $offlineCostNet = (float) DB::table('sale_items')
            ->whereIn('sale_id', $validSaleIds)
            ->selectRaw(
                'COALESCE(SUM(quantity * cost), 0) AS total_cost'
            )
            ->value('total_cost');

        /*
        |--------------------------------------------------------------------------
        | 11. ต้นทุนออนไลน์
        |--------------------------------------------------------------------------
        */

        $onlineCostNet = (float) DB::table('order_items')
            ->whereIn('order_id', $validOnlineOrderIds)
            ->selectRaw(
                'COALESCE(SUM(quantity * cost), 0) AS total_cost'
            )
            ->value('total_cost');

        /*
        |--------------------------------------------------------------------------
        | 12. ต้นทุนและกำไรสุทธิ
        |--------------------------------------------------------------------------
        */

        $totalCostNet = $offlineCostNet + $onlineCostNet;
        $netProfitNet = $totalSalesNet - $totalCostNet;

        /*
         * เก็บชื่อตัวแปรเดิมไว้
         */
        $totalCost = $totalCostNet;
        $netProfit = $netProfitNet;

        /*
        |--------------------------------------------------------------------------
        | 13. หมวดหมู่สินค้า
        |--------------------------------------------------------------------------
        */

        $categories = DB::table('categories')
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 14. ส่งข้อมูลไปยัง View
        |--------------------------------------------------------------------------
        */

        return view('reports.daily', compact(
            'sales',
            'onlineOrders',
            'categories',
            'startDate',
            'endDate',

            // ตัวแปรเดิม
            'totalSales',
            'onlineSalesTotal',
            'chartLabels',
            'chartData',
            'totalCost',
            'netProfit',
            'topProducts',
            'topOnlineProducts',
            'topAllProducts',

            // ตัวแปรใหม่ที่ View ต้องการ
            'netProfitNet',
            'totalCostNet',
            'chartLabelsNet',
            'chartDataNet',
            'topAllProductsNet'
        ));
    }
}