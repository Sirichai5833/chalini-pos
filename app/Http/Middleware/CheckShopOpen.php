<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // เปลี่ยนมาใช้ DB ดึงข้อมูลโดยตรง ปลอดภัยและชัวร์ที่สุด
use Carbon\Carbon;

class CheckShopOpen
{
    public function handle(Request $request, Closure $next)
    {
        // ❗ admin ผ่านได้เสมอ
        if (Auth::check() && Auth::user()->role === 'admin') {
    return $next($request);
}

        // ✅ แก้ไขจุดนี้: ดึงค่าจากตาราง settings ตามโครงสร้างฐานข้อมูลของคุณ
        $open = DB::table('settings')->where('key', 'open_time')->value('value');
        $close = DB::table('settings')->where('key', 'close_time')->value('value');

        // ถ้ายังไม่ตั้งเวลา → ผ่าน
        if (!$open || !$close) {
            return $next($request);
        }

        // จัดการเรื่อง Timezone ให้เป็นเวลาไทยปัจจุบัน
        $now = Carbon::now('Asia/Bangkok');
        $currentTime = $now->format('H:i');

        // สร้าง Object เวลาสำหรับการเปรียบเทียบ
        $openTime = Carbon::createFromFormat('H:i', $open);
        $closeTime = Carbon::createFromFormat('H:i', $close);

        // กรณีปิดข้ามวัน (เช่น เปิด 17:00 ปิด 02:00 ของอีกวัน)
        if ($closeTime->lt($openTime)) {
            $isOpen = ($currentTime >= $open || $currentTime <= $close);
        } else {
            // กรณีเปิด-ปิดในวันเดียวกัน (เช่น เปิด 08:00 ปิด 17:00)
            $isOpen = $now->between($openTime, $closeTime);
        }

        if (!$isOpen) {
            // ✅ แนะนำเพิ่มคำสั่งล็อกเอาต์พนักงานออกด้วย (ถ้าพนักงานค้างอยู่ในระบบ) เพื่อให้กลับไปหน้า login สวยๆ
            if (Auth::check()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            return redirect()->route('login')
                ->withErrors(['shop_closed' => 'ร้านปิดให้บริการอยู่ กรุณากลับมาใหม่ในเวลาเปิดทำการ (เวลาเปิด: ' . $open . ' - ' . $close . ' น.)']);
        }

        return $next($request);
    }
}