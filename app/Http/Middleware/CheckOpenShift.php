<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Shift;

class CheckOpenShift
{
    public function handle(Request $request, Closure $next)
    {
        // ไม่เช็คหน้าเปิดกะ / ปิดกะ
        if ($request->routeIs('shift.*')) {
            return $next($request);
        }

        // ตรวจว่ามีกะใดกะหนึ่งเปิดอยู่หรือไม่
        $hasShift = Shift::where('user_id', $request->user()?->id)
            ->where('status', 'open')
            ->exists();

        if (!$hasShift) {
            return redirect()
                ->route('shift.open')
                ->with('warning', 'กรุณาเปิดกะก่อนเริ่มขาย');
        }

        return $next($request);
    }
}
