<?php

// app/Http/Middleware/CartMiddleware.php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Log;

class CartMiddleware
{
  public function handle($request, Closure $next)
{
    $cart = session('cart');

    $items = $cart['items'] ?? [];

    $totalItems = collect($items)->sum(function ($item) {
        return $item['quantity'] ?? 0;
    });

    view()->share('totalItems', $totalItems);
    Log::info('Cart session', ['cart' => session('cart')]);

    return $next($request);
}

}
