<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Sale;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ManagerController extends Controller
{
    public function dashboard(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | 1. สรุปยอดขายหน้าร้านวันนี้
        |--------------------------------------------------------------------------
        */

        $todaySales = Sale::query()
            ->whereDate('sale_date', today())
            ->where('status', 'completed')
            ->whereNull('cancelled_at');

        $offlineTotalSales = (float) (clone $todaySales)
            ->sum('total_price');

        $offlineTotalBills = (int) (clone $todaySales)
            ->count();

        $cashSales = (float) (clone $todaySales)
            ->where('payment_method', 'cash')
            ->sum('total_price');

        $qrSales = (float) (clone $todaySales)
            ->whereIn('payment_method', [
                'qr',
                'transfer',
                'โอน',
                'promptpay',
            ])
            ->sum('total_price');

        /*
        |--------------------------------------------------------------------------
        | 2. สรุปยอดขายออนไลน์วันนี้
        |--------------------------------------------------------------------------
        |
        | ตาราง orders ไม่มี payment_status
        | จึงนับเฉพาะ status = เสร็จสิ้น
        | และไม่รวม status = ยกเลิก
        |
        */

        $todayOnlineOrders = Order::query()
            ->whereDate('created_at', today())
            ->where('status', 'เสร็จสิ้น');

        $onlineTotalSales = (float) (clone $todayOnlineOrders)
            ->sum('total_amount');

        $onlineTotalBills = (int) (clone $todayOnlineOrders)
            ->count();

        $onlineQrSales = (float) (clone $todayOnlineOrders)
            ->where('payment_method', 'โอนผ่านบัญชีธนาคาร')
            ->sum('total_amount');

        $onlineCodSales = (float) (clone $todayOnlineOrders)
            ->where('payment_method', 'เก็บเงินปลายทาง')
            ->sum('total_amount');

        $totalSales = $offlineTotalSales + $onlineTotalSales;
        $totalBills = $offlineTotalBills + $onlineTotalBills;

        /*
        |--------------------------------------------------------------------------
        | 3. กะที่กำลังเปิดอยู่
        |--------------------------------------------------------------------------
        */

        $validSale = static function ($query) {
            $query
                ->where('status', 'completed')
                ->whereNull('cancelled_at');
        };

        $openShifts = Shift::query()
            ->with([
                'user',
                'shiftType',
            ])
            ->withCount([
                'sales as sales_count' => $validSale,
            ])
            ->withSum([
                'sales as sales_sum_total_price' => $validSale,
            ], 'total_price')
            ->withSum([
                'sales as cash_sales_sum_total_price' => function ($query) {
                    $query
                        ->where('status', 'completed')
                        ->whereNull('cancelled_at')
                        ->where('payment_method', 'cash');
                },
            ], 'total_price')
            ->withSum([
                'sales as qr_sales_sum_total_price' => function ($query) {
                    $query
                        ->where('status', 'completed')
                        ->whereNull('cancelled_at')
                        ->whereIn('payment_method', [
                            'qr',
                            'transfer',
                            'โอน',
                            'promptpay',
                        ]);
                },
            ], 'total_price')
            ->where('status', 'open')
            ->latest('opened_at')
            ->paginate(15);

        foreach ($openShifts as $shift) {
            $shift->offline_sales = (float) (
                $shift->sales_sum_total_price ?? 0
            );

            $shift->offline_bills = (int) (
                $shift->sales_count ?? 0
            );

            $shift->cash_sales = (float) (
                $shift->cash_sales_sum_total_price ?? 0
            );

            $shift->qr_sales = (float) (
                $shift->qr_sales_sum_total_price ?? 0
            );

            /*
             * Order ไม่มี shift_id
             * จึงจับยอดออนไลน์ด้วยช่วงเวลาเปิดกะถึงเวลาปัจจุบัน
             */
            $shiftOnlineOrders = Order::query()
                ->whereBetween('created_at', [
                    $shift->opened_at,
                    now(),
                ])
                ->where('status', 'เสร็จสิ้น');

            $shift->online_sales = (float) (clone $shiftOnlineOrders)
                ->sum('total_amount');

            $shift->online_bills = (int) (clone $shiftOnlineOrders)
                ->count();

            $shift->online_qr_sales = (float) (clone $shiftOnlineOrders)
                ->where(
                    'payment_method',
                    'โอนผ่านบัญชีธนาคาร'
                )
                ->sum('total_amount');

            $shift->online_cod_sales = (float) (clone $shiftOnlineOrders)
                ->where(
                    'payment_method',
                    'เก็บเงินปลายทาง'
                )
                ->sum('total_amount');

            $shift->total_sales =
                $shift->offline_sales
                + $shift->online_sales;

            $shift->total_bills =
                $shift->offline_bills
                + $shift->online_bills;

            $cashIn = (float) ($shift->cash_in ?? 0);
            $cashOut = (float) ($shift->cash_out ?? 0);

            /*
             * เงินสดที่ควรมีไม่นับยอดออนไลน์
             * เพราะ QR และ COD ไม่ใช่เงินสดในลิ้นชัก
             */
            $shift->calculated_expected_cash =
                (float) $shift->opening_cash
                + $shift->cash_sales
                + $cashIn
                - $cashOut;

            $shift->cash_in_total = $cashIn;
            $shift->cash_out_total = $cashOut;
        }

        /*
        |--------------------------------------------------------------------------
        | 4. ประวัติกะย้อนหลัง
        |--------------------------------------------------------------------------
        */

        $range = $request->get('range', 'today');

        $from = now()->startOfDay();
        $to = now()->endOfDay();

        switch ($range) {
            case 'yesterday':
                $from = now()
                    ->subDay()
                    ->startOfDay();

                $to = now()
                    ->subDay()
                    ->endOfDay();
                break;

            case '7days':
                $from = now()
                    ->subDays(6)
                    ->startOfDay();

                $to = now()
                    ->endOfDay();
                break;

            case 'month':
                $from = now()
                    ->startOfMonth();

                $to = now()
                    ->endOfDay();
                break;

            case 'custom':
                if ($request->filled('from')) {
                    $from = Carbon::parse(
                        $request->from
                    )->startOfDay();
                }

                if ($request->filled('to')) {
                    $to = Carbon::parse(
                        $request->to
                    )->endOfDay();
                }
                break;
        }

        $closedShifts = Shift::query()
            ->with([
                'user',
                'shiftType',
                'closedBy',
            ])
            ->withCount([
                'sales as sales_count' => $validSale,
            ])
            ->withSum([
                'sales as offline_sales' => $validSale,
            ], 'total_price')
            ->where('status', 'closed')
            ->whereBetween('opened_at', [$from, $to])
            ->latest('opened_at')
            ->paginate(15)
            ->withQueryString();

        foreach ($closedShifts as $shift) {
            $closedAt = $shift->closed_at ?? $shift->opened_at;

            $shiftOnlineOrders = Order::query()
                ->whereBetween('created_at', [
                    $shift->opened_at,
                    $closedAt,
                ])
                ->where('status', 'เสร็จสิ้น');

            $shift->online_sales = (float) (clone $shiftOnlineOrders)
                ->sum('total_amount');

            $shift->online_bills = (int) (clone $shiftOnlineOrders)
                ->count();

            $shift->total_sales =
                (float) ($shift->offline_sales ?? 0)
                + $shift->online_sales;
        }

        return view(
            'manager.dashboard',
            compact(
                'totalSales',
                'totalBills',
                'offlineTotalSales',
                'offlineTotalBills',
                'cashSales',
                'qrSales',
                'onlineTotalSales',
                'onlineTotalBills',
                'onlineQrSales',
                'onlineCodSales',
                'openShifts',
                'closedShifts',
                'range'
            )
        );
    }
}