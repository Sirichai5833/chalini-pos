<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetOtpMail;
use App\Models\PasswordOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    public function showRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $otp = random_int(100000, 999999);
        $expiresAt = now()->addMinutes(10);

        PasswordOtp::updateOrCreate(
            [
                'email' => $request->email,
            ],
            [
                'otp' => $otp,
                'expires_at' => $expiresAt,
                'verified' => false,
            ]
        );

        Mail::to($request->email)->send(new PasswordResetOtpMail($otp));

        return redirect()
            ->route('password.otp.verify')
            ->with([
                'status' => 'ส่งรหัส OTP ไปยังอีเมลของคุณแล้ว',
                'email' => $request->email,
            ]);
    }

    public function showVerifyForm(Request $request)
    {
        return view('auth.verify-otp', [
            'email' => $request->session()->get('email', old('email')),
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'otp' => ['required', 'digits:6'],
        ]);

        $otpRecord = PasswordOtp::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('expires_at', '>', now())
            ->first();

        if (! $otpRecord) {
            return back()
                ->withErrors(['otp' => 'รหัส OTP ไม่ถูกต้องหรือหมดอายุ'])
                ->withInput();
        }

        if ($otpRecord->verified) {
            return back()
                ->withErrors(['otp' => 'รหัส OTP นี้ถูกใช้ไปแล้ว'])
                ->withInput();
        }

        $otpRecord->update(['verified' => true]);
        $request->session()->put('password_reset_email', $request->email);

        return redirect()
            ->route('password.reset')
            ->with('status', 'ยืนยัน OTP แล้ว โปรดตั้งรหัสผ่านใหม่');
    }
}
