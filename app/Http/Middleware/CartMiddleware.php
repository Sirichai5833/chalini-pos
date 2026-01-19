<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class CartMiddleware
{
    public function handle($request, Closure $next)
    {
        $cart = session('cart');

        // ถ้า cart ไม่มี หรือไม่ใช่ array → ปลอดภัยไว้ก่อน
        if (!is_array($cart)) {
            view()->share('totalItems', 0);
            return $next($request);
        }

        // ถ้า cart มีโครงสร้างแบบมี items
        $items = $cart['items'] ?? [];

        if (!is_array($items)) {
            $items = [];
        }

        $totalItems = 0;

        foreach ($items as $item) {
            if (is_array($item) && isset($item['quantity'])) {
                $totalItems += (int) $item['quantity'];
            }
        }

        view()->share('totalItems', $totalItems);

        Log::info('CartMiddleware totalItems', ['totalItems' => $totalItems]);
dd(session('cart'));

        return $next($request);
    }
}
