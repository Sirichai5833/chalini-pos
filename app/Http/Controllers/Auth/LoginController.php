<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |---------------------------------------------------------------------------
    | Login Controller
    |---------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */



    /**
     * Handle the login process.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        // กำหนดกฎการตรวจสอบข้อมูลจากฟอร์ม
        $this->validateLogin($request);

        // พยายามเข้าสู่ระบบ
        if ($this->attemptLogin($request)) {
            // ตรวจสอบผู้ใช้และทำการยืนยันตัวตนสำเร็จ
            return $this->sendLoginResponse($request);
        }

        // ถ้าเข้าสู่ระบบไม่สำเร็จ จะกลับไปที่หน้า login พร้อมแสดงข้อความผิดพลาด
        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Log the user out of the application.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        // ทำการออกจากระบบ
        $this->guard()->logout();

        // ลบข้อมูลผู้ใช้ที่มีอยู่ใน session
        $request->session()->invalidate();

        // สร้าง session ใหม่
        $request->session()->regenerateToken();

        // รีไดเรกไปยังหน้า login
        return redirect()->route('login');
    }
}
