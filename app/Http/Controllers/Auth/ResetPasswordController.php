<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordOtp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    public function showResetForm(Request $request)
    {
        $email = $request->session()->get('password_reset_email');

        if (! $email) {
            return redirect()
                ->route('password.request')
                ->withErrors(['email' => 'กรุณายืนยัน OTP ก่อนตั้งรหัสผ่านใหม่']);
        }

        $otpRecord = PasswordOtp::where('email', $email)
            ->where('verified', true)
            ->first();

        if (! $otpRecord) {
            return redirect()
                ->route('password.request')
                ->withErrors(['email' => 'OTP ยังไม่ถูกยืนยันหรือถูกใช้งานแล้ว']);
        }

        return view('auth.reset-password', [
            'email' => $email,
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $otpRecord = PasswordOtp::where('email', $request->email)
            ->where('verified', true)
            ->first();

        if (! $otpRecord) {
            return back()
                ->withErrors(['email' => 'OTP ยังไม่ถูกยืนยันหรือหมดอายุ'])
                ->withInput();
        }

        $user = User::where('email', $request->email)->first();
        $user->forceFill([
            'password' => Hash::make($request->password),
        ])->save();

        $otpRecord->delete();
        $request->session()->forget('password_reset_email');

        return redirect()
            ->route('login')
            ->with('status', 'รีเซ็ตรหัสผ่านสำเร็จ โปรดเข้าสู่ระบบด้วยรหัสผ่านใหม่');
    }
}
