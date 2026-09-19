<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApartmentMember
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $member = $user->memberProfile()
            ->with('room')
            ->first();

        $isAllowed = $member
            && $member->status === 'active'
            && $member->verified_at !== null
            && $member->room
            && (bool) $member->room->is_active;

        if (!$isAllowed) {
            return redirect()
                ->route('online.products.index')
                ->with('error', 'เฉพาะสมาชิกอพาร์ตเมนต์ที่ผ่านการยืนยันเท่านั้น');
        }

        return $next($request);
    }
}
