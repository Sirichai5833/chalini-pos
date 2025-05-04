<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
                return redirect()->route('sale'); // ไปหน้าขายของแอดมิน
            }

            return redirect()->route('login'); // fallback
        }

        // login fail
        return back()->withErrors([
            'email' => 'ข้อมูลไม่ถูกต้อง',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
