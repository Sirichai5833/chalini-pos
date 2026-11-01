<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Sale;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ManagerController extends Controller
{
    /**
     * วิธีชำระเงิน QR / เงินโอนหน้าร้าน
     */
    private function storeTransferMethods(): array
    {
        return [
            'qr',
            'qrcode',
            'qr_code',
            'qr code',
            'promptpay',
            'prompt_pay',
            'transfer',
            'bank_transfer',
            'bank transfer',
            'โอน',
            'โอนเงิน',
            'โอนผ่านบัญชีธนาคาร',
            'พร้อมเพย์',
        ];
    }

    /**
     * วิธีชำระเงิน QR / เงินโอนออนไลน์
     */
    private function onlineTransferMethods(): array
    {
        return [
            'โอนผ่านบัญชีธนาคาร',
            'โอนเงิน',
            'transfer',
            'bank_transfer',
            'bank transfer',
            'qr',
            'qrcode',
            'qr_code',
            'qr code',
            'promptpay',
            'prompt_pay',
            'พร้อมเพย์',
        ];
    }

    /**
     * วิธีชำระเงิน COD
     */
    private function onlineCodMethods(): array
    {
        return [
            'เก็บเงินปลายทาง',
            'cod',
            'cash_on_delivery',
        ];
    }

    /**
     * สรุปยอดขายหน้าร้านตามกะ
     */
    private function summarizeStoreSales($shiftId): array
    {
        $query = Sale::query()
            ->where('shift_id', $shiftId)
            ->where('status', 'completed')
            ->whereNull('cancelled_at');

        $cash = (float) (clone $query)
            ->where('payment_method', 'cash')
            ->sum('total_price');

        $qr = (float) (clone $query)
            ->whereIn(
                'payment_method',
                $this->storeTransferMethods()
            )
            ->sum('total_price');

        $card = (float) (clone $query)
            ->where('payment_method', 'card')
            ->sum('total_price');

        $credit = (float) (clone $query)
            ->where('payment_method', 'credit')
            ->sum('total_price');

        $total = (float) (clone $query)
            ->sum('total_price');

        $bills = (int) (clone $query)
            ->count();

        return [
            'cash' => $cash,
            'qr' => $qr,
            'card' => $card,
            'credit' => $credit,
            'total' => $total,
            'bills' => $bills,
        ];
    }

    /**
     * สรุปยอดขายออนไลน์ตามช่วงเวลา
     */
    private function summarizeOnlineSales($from, $to): array
    {
        $query = Order::query()
            ->whereBetween('created_at', [$from, $to])
            ->where('status', 'เสร็จสิ้น');

        $qr = (float) (clone $query)
            ->whereIn(
                'payment_method',
                $this->onlineTransferMethods()
            )
            ->sum('total_amount');

        $cod = (float) (clone $query)
            ->whereIn(
                'payment_method',
                $this->onlineCodMethods()
            )
            ->sum('total_amount');

        $total = (float) (clone $query)
            ->sum('total_amount');

        $bills = (int) (clone $query)
            ->count();

        return [
            'qr' => $qr,
            'cod' => $cod,
            'total' => $total,
            'bills' => $bills,
        ];
    }

    /**
     * คำนวณเงินของกะ
     */
    private function calculateShiftMoney(
        $shift,
        array $store,
        array $online
    ): array {
        $openingCash = (float) $shift->opening_cash;

        $cashIn = (float) ($shift->cash_in ?? 0);
        $cashOut = (float) ($shift->cash_out ?? 0);

        // เงินสดที่รับจริง
        $combinedCash =
            $store['cash']
            + $online['cod'];

        // เงิน QR / เงินโอน
        $combinedQr =
            $store['qr']
            + $online['qr'];

        // เงินจากบัตรและเครดิต
        $combinedOther =
            $store['card']
            + $store['credit'];

        // ยอดขายทั้งหมด
        $totalSales =
            $combinedCash
            + $combinedQr
            + $combinedOther;

        // เงินสดที่ควรมีในลิ้นชัก
        $expectedCash =
            $openingCash
            + $combinedCash
            + $cashIn
            - $cashOut;

        // ยอด QR ที่ควรได้รับ
        $expectedQr = $combinedQr;

        // เงินที่คาดว่าจะนำส่ง โดยเก็บเงินทอนตั้งต้นไว้
        $estimatedRemit =
            $expectedCash - $openingCash;

        return [
            'opening_cash' => $openingCash,

            'cash_sales' => $store['cash'],
            'qr_sales' => $store['qr'],

            'online_qr_sales' => $online['qr'],
            'online_cod_sales' => $online['cod'],

            'combined_cash_sales' => $combinedCash,
            'combined_qr_sales' => $combinedQr,
            'combined_other_sales' => $combinedOther,

            'total_sales' => $totalSales,

            'offline_sales' => $store['total'],
            'offline_bills' => $store['bills'],

            'online_sales' => $online['total'],
            'online_bills' => $online['bills'],

            'total_bills' =>
                $store['bills'] + $online['bills'],

            'cash_in_total' => $cashIn,
            'cash_out_total' => $cashOut,

            'calculated_expected_cash' => $expectedCash,
            'expected_cash' => $expectedCash,
            'expected_qr' => $expectedQr,

            'cash_to_keep' => $openingCash,
            'cash_to_remit' => $estimatedRemit,
        ];
    }

    public function dashboard(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | 1. ยอดขายวันนี้
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
            ->whereIn(
                'payment_method',
                $this->storeTransferMethods()
            )
            ->sum('total_price');

        /*
        |--------------------------------------------------------------------------
        | 2. ยอดขายออนไลน์วันนี้
        |--------------------------------------------------------------------------
        */

        $todayOnlineOrders = Order::query()
            ->whereDate('created_at', today())
            ->where('status', 'เสร็จสิ้น');

        $onlineTotalSales = (float) (clone $todayOnlineOrders)
            ->sum('total_amount');

        $onlineTotalBills = (int) (clone $todayOnlineOrders)
            ->count();

        $onlineQrSales = (float) (clone $todayOnlineOrders)
            ->whereIn(
                'payment_method',
                $this->onlineTransferMethods()
            )
            ->sum('total_amount');

        $onlineCodSales = (float) (clone $todayOnlineOrders)
            ->whereIn(
                'payment_method',
                $this->onlineCodMethods()
            )
            ->sum('total_amount');

        $totalSales =
            $offlineTotalSales + $onlineTotalSales;

        $totalBills =
            $offlineTotalBills + $onlineTotalBills;

        // ยอดขายรวมแยกตามช่องทาง
        $combinedCashSales =
            $cashSales + $onlineCodSales;

        $combinedQrSales =
            $qrSales + $onlineQrSales;

        /*
        |--------------------------------------------------------------------------
        | 3. กะที่กำลังเปิดอยู่
        |--------------------------------------------------------------------------
        */

        $openShifts = Shift::query()
            ->with([
                'user',
                'shiftType',
            ])
            ->where('status', 'open')
            ->latest('opened_at')
            ->paginate(15);

        foreach ($openShifts as $shift) {

            $store = $this->summarizeStoreSales(
                $shift->id
            );

            $online = $this->summarizeOnlineSales(
                $shift->opened_at,
                now()
            );

            $money = $this->calculateShiftMoney(
                $shift,
                $store,
                $online
            );

            foreach ($money as $key => $value) {
                $shift->{$key} = $value;
            }

            /*
             * ยอดขายแยกตามประเภท
             */
            $shift->cash_sales = $store['cash'];
            $shift->qr_sales = $store['qr'];

            $shift->online_qr_sales = $online['qr'];
            $shift->online_cod_sales = $online['cod'];

            $shift->online_sales = $online['total'];
            $shift->online_bills = $online['bills'];

            $shift->sales_count =
                $store['bills'];

            $shift->sales_sum_total_price =
                $store['total'];
        }

        /*
        |--------------------------------------------------------------------------
        | 4. กรองประวัติกะย้อนหลัง
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

                $to = now()->endOfDay();

                break;

            case 'month':

                $from = now()->startOfMonth();
                $to = now()->endOfDay();

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

        /*
        |--------------------------------------------------------------------------
        | 5. ประวัติกะที่ปิดแล้ว
        |--------------------------------------------------------------------------
        */

        $closedShifts = Shift::query()
            ->with([
                'user',
                'shiftType',
                'closedBy',
            ])
            ->where('status', 'closed')
            ->whereBetween('opened_at', [$from, $to])
            ->latest('opened_at')
            ->paginate(15)
            ->withQueryString();

        foreach ($closedShifts as $shift) {

            $closedAt = $shift->closed_at
                ?? $shift->opened_at;

            $store = $this->summarizeStoreSales(
                $shift->id
            );

            $online = $this->summarizeOnlineSales(
                $shift->opened_at,
                $closedAt
            );

            $money = $this->calculateShiftMoney(
                $shift,
                $store,
                $online
            );

            foreach ($money as $key => $value) {
                $shift->{$key} = $value;
            }

            /*
             * เงินที่นับได้จริงตอนปิดกะ
             */
            $closingCash = (float) (
                $shift->getRawOriginal('closing_cash') ?? 0
            );

            $closingQr = (float) (
                $shift->getRawOriginal('closing_qr') ?? 0
            );

            $shift->closing_cash = $closingCash;
            $shift->closing_qr = $closingQr;

            /*
             * คำนวณเงินขาด / เกินใหม่
             */
            $shift->cash_difference =
                $closingCash - $money['calculated_expected_cash'];

            $shift->qr_difference =
                $closingQr - $money['expected_qr'];

            /*
             * เงินที่นำส่งจริงหลังเก็บเงินทอนตั้งต้น
             */
            $shift->cash_to_remit =
                $closingCash - $money['opening_cash'];

            $shift->cash_to_keep =
                $money['opening_cash'];

            $shift->total_sales =
                $money['total_sales'];

            $shift->total_bills =
                $money['total_bills'];

            $shift->offline_sales =
                $store['total'];

            $shift->online_sales =
                $online['total'];

            $shift->online_bills =
                $online['bills'];
        }

        /*
        |--------------------------------------------------------------------------
        | 6. ส่งข้อมูลไปยัง Dashboard
        |--------------------------------------------------------------------------
        */

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

                'combinedCashSales',
                'combinedQrSales',

                'openShifts',
                'closedShifts',

                'range'
            )
        );
    }
}
