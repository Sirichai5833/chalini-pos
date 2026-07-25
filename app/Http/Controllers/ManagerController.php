<?php

namespace App\Http\Controllers;

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
        | 1. สรุปยอดขายวันนี้
        |--------------------------------------------------------------------------
        */

        $todaySales = Sale::query()
            ->whereDate('sale_date', today());

        $totalSales = (clone $todaySales)
            ->sum('total_price');

        $totalBills = (clone $todaySales)
            ->count();

        $cashSales = (clone $todaySales)
            ->where('payment_method', 'cash')
            ->sum('total_price');

        $qrSales = (clone $todaySales)
            ->whereIn('payment_method', [
                'qr',
                'transfer',
                'โอน',
                'promptpay',
            ])
            ->sum('total_price');


        /*
        |--------------------------------------------------------------------------
        | 2. กะที่กำลังเปิดอยู่
        |--------------------------------------------------------------------------
        */

        $openShifts = Shift::with([
            'user',
            'shiftType',
        ])
            ->where('status', 'open')
            ->latest('opened_at')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | 3. คำนวณข้อมูลของแต่ละกะ
        |--------------------------------------------------------------------------
        */

        foreach ($openShifts as $shift) {

            $shiftSales = Sale::query()
                ->where('shift_id', $shift->id);

            $shift->total_sales = (clone $shiftSales)
                ->sum('total_price');

            $shift->total_bills = (clone $shiftSales)
                ->count();

            $shift->cash_sales = (clone $shiftSales)
                ->where('payment_method', 'cash')
                ->sum('total_price');

            $shift->qr_sales = (clone $shiftSales)
                ->whereIn('payment_method', [
                    'qr',
                    'transfer',
                    'โอน',
                    'promptpay',
                ])
                ->sum('total_price');


            /*
            |--------------------------------------------------------------------------
            | เงินสดที่ควรมี
            |--------------------------------------------------------------------------
            |
            | opening_cash
            | + cash sales
            | + cash in
            | - cash out
            |
            |--------------------------------------------------------------------------
            */


            /*
             * ถ้าระบบของคุณบันทึก Cash In / Cash Out
             * อยู่ใน columns ของ shifts ให้แก้ชื่อด้านล่างตามจริง
             */

            $cashIn = $shift->cash_in ?? 0;

            $cashOut = $shift->cash_out ?? 0;

            $shift->calculated_expected_cash =
                $shift->opening_cash
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


        $closedShifts = Shift::with([
            'user',
            'shiftType',
        ])
            ->where('status', 'closed')
            ->whereBetween(
                'opened_at',
                [$from, $to]
            )
            ->latest('opened_at')
            ->paginate(15)
            ->withQueryString();


        return view(
            'manager.dashboard',
            compact(
                'totalSales',
                'totalBills',
                'cashSales',
                'qrSales',
                'openShifts',
                'closedShifts',
                'range'
            )
        );
    }
}