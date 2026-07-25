<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Setting;
use App\Models\Shift;
use App\Models\ShiftType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShiftController extends Controller
{
    public function create()
    {
        $shift = Shift::where('user_id', auth()->id())
            ->where('status', 'open')
            ->first();

        // ถ้าเปิดกะไว้แล้ว ให้ส่งไปหน้า POS ทันที
        if ($shift) {
            return redirect('/online/pos');
        }

        $openingCash = Setting::where('key', 'opening_cash')->value('value');
        $shiftTypes = ShiftType::where('active', 1)->get();

        return view('shift.open', compact(
            'openingCash',
            'shiftTypes'
        ));
    }

    public function store(Request $request)
    {
        $openingCash = Setting::where('key', 'opening_cash')->value('value');

        Shift::create([
            'user_id' => auth()->id(),
            'shift_type_id' => $request->shift_type_id,
            'opening_cash' => $openingCash,
            'opened_at' => now(),
            'status' => 'open',
        ]);

        return redirect()->route('sale')
            ->with('success', 'เปิดกะเรียบร้อย');
    }

    public function close()
    {
        // ⚡️ ระบุ user_id ของคนที่ล็อกอิน เพื่อกันไม่ให้ดึงกะของคนอื่นมาปิด
        $shift = Shift::with('shiftType')
            ->where('user_id', auth()->id())
            ->where('status', 'open')
            ->first();

        // ⚡️ ถ้ายังไม่ได้เปิดกะ ให้ส่งกลับไปหน้าเปิดกะพร้อมแจ้งเตือน
        if (!$shift) {
            return redirect()
                ->route('shift.create')
                ->with('error', 'คุณยังไม่ได้เปิดกะ กรุณาเปิดกะก่อนปิดกะ');
        }

        $cashSales = Sale::where('shift_id', $shift->id)
            ->where('payment_method', 'cash')
            ->sum('total_price');

        $qrSales = Sale::where('shift_id', $shift->id)
            ->where('payment_method', 'qr')
            ->sum('total_price');

        $cardSales = Sale::where('shift_id', $shift->id)
            ->where('payment_method', 'card')
            ->sum('total_price');

        $creditSales = Sale::where('shift_id', $shift->id)
            ->where('payment_method', 'credit')
            ->sum('total_price');

        $expectedCash = $shift->opening_cash + $cashSales;

        return view('shift.close', compact(
            'shift',
            'cashSales',
            'qrSales',
            'cardSales',
            'creditSales',
            'expectedCash'
        ));
    }

    public function update(Request $request)
    {
        $request->validate([
            'closing_cash' => 'required|numeric|min:0',
            'note' => 'nullable|string'
        ]);

        // ⚡️ ระบุ user_id ในขั้นตอนอัปเดตปิดกะด้วยเช่นกัน
        $shift = Shift::where('user_id', auth()->id())
            ->where('status', 'open')
            ->firstOrFail();

        $cashSales = Sale::where('shift_id', $shift->id)
            ->where('payment_method', 'cash')
            ->sum('total_price');

        $expectedCash = $shift->opening_cash + $cashSales;

        $difference = $request->closing_cash - $expectedCash;

        $shift->update([
            'expected_cash' => $expectedCash,
            'closing_cash' => $request->closing_cash,
            'cash_difference' => $difference,
            'closed_at' => now(),
            'status' => 'closed',
            'note' => $request->note
        ]);

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'ปิดกะเรียบร้อย');
    }
}