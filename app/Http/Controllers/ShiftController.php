<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Sale;
use App\Models\Setting;
use App\Models\Shift;
use App\Models\ShiftType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShiftController extends Controller
{
    /**
     * วิธีชำระหน้าร้านที่ถือเป็น QR / เงินโอน
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
     * วิธีชำระออนไลน์ที่ถือเป็น QR / เงินโอน
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
     * วิธีชำระออนไลน์แบบเก็บเงินปลายทาง
     */
    private function onlineCodMethods(): array
    {
        return [
            'เก็บเงินปลายทาง',
            'cod',
            'cash_on_delivery',
        ];
    }

    public function create()
{
    $shift = Shift::query()
        ->where('user_id', auth()->id())
        ->where('status', 'open')
        ->first();

    if ($shift) {
        return redirect()->route('sale');
    }

    $openingCash = (float) Setting::query()
        ->where('key', 'opening_cash')
        ->value('value');

    return view('shift.open', compact('openingCash'));
}

    public function store(Request $request)
    {
        $validated = $request->validate([
       
        ]);

        $alreadyOpen = Shift::query()
            ->where('user_id', auth()->id())
            ->where('status', 'open')
            ->exists();

        if ($alreadyOpen) {
            return redirect()
                ->route('sale')
                ->with('info', 'คุณเปิดกะไว้แล้ว');
        }

        $openingCash = (float) Setting::query()
            ->where('key', 'opening_cash')
            ->value('value');

        Shift::create([
            'user_id' => auth()->id(),
            'shift_type_id' => null,
            'opening_cash' => $openingCash,
            'opened_at' => now(),
            'status' => 'open',
        ]);

        return redirect()
            ->route('sale')
            ->with('success', 'เปิดกะเรียบร้อย');
    }

    public function close()
    {
        $shift = Shift::query()
            ->with('shiftType')
            ->where('user_id', auth()->id())
            ->where('status', 'open')
            ->first();

        if (!$shift) {
            return redirect()
                ->route('shift.create')
                ->with(
                    'error',
                    'คุณยังไม่ได้เปิดกะ กรุณาเปิดกะก่อนปิดกะ'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | 1. ยอดขายหน้าร้านสุทธิ
        |--------------------------------------------------------------------------
        |
        | นับเฉพาะ completed และ cancelled_at ต้องเป็น null
        |
        */

        $validSalesQuery = Sale::query()
            ->where('shift_id', $shift->id)
            ->where('status', 'completed')
            ->whereNull('cancelled_at');

        $cashSalesNet = (float) (clone $validSalesQuery)
            ->where('payment_method', 'cash')
            ->sum('total_price');

        $qrSalesNet = (float) (clone $validSalesQuery)
            ->whereIn(
                'payment_method',
                $this->storeTransferMethods()
            )
            ->sum('total_price');

        $cardSalesNet = (float) (clone $validSalesQuery)
            ->where('payment_method', 'card')
            ->sum('total_price');

        $creditSalesNet = (float) (clone $validSalesQuery)
            ->where('payment_method', 'credit')
            ->sum('total_price');

        $offlineSalesNet =
            $cashSalesNet
            + $qrSalesNet
            + $cardSalesNet
            + $creditSalesNet;

        /*
        |--------------------------------------------------------------------------
        | 2. ยอดขายออนไลน์สุทธิ
        |--------------------------------------------------------------------------
        |
        | ตาราง orders ไม่มี payment_status
        | จึงนับเฉพาะ order ที่ status = เสร็จสิ้น
        | ทำให้ order สถานะ ยกเลิก ไม่ถูกนำมารวม
        |
        */

        $completedOnlineOrdersQuery = Order::query()
            ->whereBetween('created_at', [
                $shift->opened_at,
                now(),
            ])
            ->where('status', 'เสร็จสิ้น');

        $onlineQrSalesNet = (float) (clone $completedOnlineOrdersQuery)
            ->whereIn(
                'payment_method',
                $this->onlineTransferMethods()
            )
            ->sum('total_amount');

        $onlineCodSalesNet = (float) (clone $completedOnlineOrdersQuery)
            ->whereIn(
                'payment_method',
                $this->onlineCodMethods()
            )
            ->sum('total_amount');

        $onlineSalesNet =
            $onlineQrSalesNet
            + $onlineCodSalesNet;

        /*
         * รวมยอดตามช่องทางรับเงินจริง
         * - เงินสดรวม: เงินสดหน้าร้าน + COD ที่เสร็จสิ้นและรับเงินจริงแล้ว
         * - QR รวม: QR/เงินโอนหน้าร้าน + QR/เงินโอนออนไลน์
         */
        $combinedCashSalesNet =
            $cashSalesNet
            + $onlineCodSalesNet;

        $combinedQrSalesNet =
            $qrSalesNet
            + $onlineQrSalesNet;

        $combinedOtherSalesNet =
            $cardSalesNet
            + $creditSalesNet;

        $totalSalesNet =
            $combinedCashSalesNet
            + $combinedQrSalesNet
            + $combinedOtherSalesNet;

        /*
        |--------------------------------------------------------------------------
        | 3. รายการยกเลิก
        |--------------------------------------------------------------------------
        |
        | ใช้แสดงแจ้งเตือนเท่านั้น
        | ไม่ถูกนำไปคำนวณยอดขายหรือเงินสดที่ควรมี
        |
        */

        $cancelledSalesQuery = Sale::query()
            ->where('shift_id', $shift->id)
            ->where(function ($query) {
                $query
                    ->where('status', 'cancelled')
                    ->orWhereNotNull('cancelled_at');
            });

        $cancelledCashSales = (float) (clone $cancelledSalesQuery)
            ->where('payment_method', 'cash')
            ->sum('total_price');

        $cancelledQrSales = (float) (clone $cancelledSalesQuery)
            ->whereIn(
                'payment_method',
                $this->storeTransferMethods()
            )
            ->sum('total_price');

        $cancelledOnlineOrdersQuery = Order::query()
            ->whereBetween('created_at', [
                $shift->opened_at,
                now(),
            ])
            ->where('status', 'ยกเลิก');

        $cancelledOnlineQrSales =
            (float) (clone $cancelledOnlineOrdersQuery)
                ->whereIn(
                    'payment_method',
                    $this->onlineTransferMethods()
                )
                ->sum('total_amount');

        $cancelledOnlineCodSales =
            (float) (clone $cancelledOnlineOrdersQuery)
                ->whereIn(
                    'payment_method',
                    $this->onlineCodMethods()
                )
                ->sum('total_amount');

        /*
        |--------------------------------------------------------------------------
        | 4. เงินสดที่ควรมี
        |--------------------------------------------------------------------------
        |
        | เงินโอน, QR, บัตร และยอดออนไลน์เป็นยอดขาย
        | แต่ไม่ใช่เงินสดในลิ้นชัก
        |
        */

        $expectedCashNet =
            (float) $shift->opening_cash
            + $combinedCashSalesNet;

        $expectedQrNet = $combinedQrSalesNet;

        return view('shift.close', compact(
            'shift',
            'cashSalesNet',
            'qrSalesNet',
            'cardSalesNet',
            'creditSalesNet',
            'offlineSalesNet',
            'onlineQrSalesNet',
            'onlineCodSalesNet',
            'onlineSalesNet',
            'combinedCashSalesNet',
            'combinedQrSalesNet',
            'combinedOtherSalesNet',
            'totalSalesNet',
            'cancelledCashSales',
            'cancelledQrSales',
            'cancelledOnlineQrSales',
            'cancelledOnlineCodSales',
            'expectedCashNet',
            'expectedQrNet'
        ));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'closing_cash' => [
                'required',
                'numeric',
                'min:0',
            ],
            'closing_qr' => [
                'required',
                'numeric',
                'min:0',
            ],
            'note' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $shift = Shift::query()
            ->where('user_id', auth()->id())
            ->where('status', 'open')
            ->firstOrFail();

        /*
         * คำนวณยอดใหม่จากฐานข้อมูลตอนยืนยันปิดกะ
         * เพื่อไม่เชื่อค่าจากหน้าเว็บ และไม่รวมรายการยกเลิก
         */
        $validSalesQuery = Sale::query()
            ->where('shift_id', $shift->id)
            ->where('status', 'completed')
            ->whereNull('cancelled_at');

        $cashSalesNet = (float) (clone $validSalesQuery)
            ->where('payment_method', 'cash')
            ->sum('total_price');

        $qrSalesNet = (float) (clone $validSalesQuery)
            ->whereIn(
                'payment_method',
                $this->storeTransferMethods()
            )
            ->sum('total_price');

        $completedOnlineOrdersQuery = Order::query()
            ->whereBetween('created_at', [
                $shift->opened_at,
                now(),
            ])
            ->where('status', 'เสร็จสิ้น');

        $onlineQrSalesNet = (float) (clone $completedOnlineOrdersQuery)
            ->whereIn(
                'payment_method',
                $this->onlineTransferMethods()
            )
            ->sum('total_amount');

        $onlineCodSalesNet = (float) (clone $completedOnlineOrdersQuery)
            ->whereIn(
                'payment_method',
                $this->onlineCodMethods()
            )
            ->sum('total_amount');

        $combinedCashSalesNet =
            $cashSalesNet
            + $onlineCodSalesNet;

        $combinedQrSalesNet =
            $qrSalesNet
            + $onlineQrSalesNet;

        $expectedCashNet =
            (float) $shift->opening_cash
            + $combinedCashSalesNet;

        $expectedQrNet = $combinedQrSalesNet;

        $closingCash = (float) $validated['closing_cash'];
        $closingQr = (float) $validated['closing_qr'];

        $cashDifference = $closingCash - $expectedCashNet;
        $qrDifference = $closingQr - $expectedQrNet;

        $shift->update([
            'expected_cash' => $expectedCashNet,
            'closing_cash' => $closingCash,
            'cash_difference' => $cashDifference,
            'expected_qr' => $expectedQrNet,
            'closing_qr' => $closingQr,
            'qr_difference' => $qrDifference,
            'closed_at' => now(),
            'status' => 'closed',
            'note' => $validated['note'] ?? null,
            'closed_by' => auth()->id(),
        ]);

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'ปิดกะเรียบร้อย');
    }
}