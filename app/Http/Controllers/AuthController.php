<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Shift;
class AuthController extends Controller
{
    public function loginPage()
    {
        return view('auth.login'); // สมมุติว่าไฟล์อยู่ที่ resources/views/auth/login.blade.php
    }
    

    public function login(Request $request)
    {
        // ตรวจสอบข้อมูลจากฟอร์ม
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();

            // 🎯 redirect ตาม role
            if ($user->role === 'member') {
                return redirect()->route('online.index'); // ไปหน้า POS ของลูกค้า
            }

            if (in_array($user->role, ['staff', 'owner', 'admin'])) {

    $hasShift = Shift::where('user_id', $user->id)
        ->where('status', 'open')
        ->exists();

    if (!$hasShift) {
        return redirect()->route('shift.open');
    }

    return redirect()->route('sale');
}

            return redirect()->route('login'); // fallback
        }

        // login fail
        return back()->withErrors([
    'email' => 'อีเมลหรือรหัสผ่านไม่ถูกต้อง',
])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
