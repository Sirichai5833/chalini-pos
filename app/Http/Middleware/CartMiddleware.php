<?php

namespace App\Http\Middleware;

use Closure;

class CartMiddleware
{
    public function handle($request, Closure $next)
    {
        $cart = session('cart');

        // 🔴 cart ไม่มี → อย่าทำอะไรต่อ
        if (!$cart || !is_array($cart)) {
            view()->share('totalItems', 0);
            return $next($request);
        }

        // รองรับทั้ง 2 แบบ: มี items หรือเป็น array ตรง ๆ
        $items = $cart['items'] ?? $cart;

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

        return $next($request);
    }
}
