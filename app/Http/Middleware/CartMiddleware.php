<?php

// app/Http/Middleware/CartMiddleware.php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Log;

class CartMiddleware
{
    public function handle($request, Closure $next)
    {
        // ดึงข้อมูลจาก session
        $cart = session('cart', []);

        // ตรวจสอบว่า cart มีข้อมูลเป็น array ของ product หรือไม่
        $totalItems = collect($cart)->sum(function ($item) {
            return isset($item['quantity']) ? $item['quantity'] : 0;
        });

        // แชร์ข้อมูลไปยังทุก view
        view()->share('totalItems', $totalItems);

        // log เพื่อตรวจสอบว่า middleware ทำงานหรือไม่
        Log::info("CartMiddleware hit with total items: $totalItems");

        return $next($request);
    }
}

