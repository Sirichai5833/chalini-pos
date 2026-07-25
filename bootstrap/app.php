<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use App\Http\Middleware\CartMiddleware;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\CheckShopOpen;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // ✅ Middleware alias (ใช้ใน routes)
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'guest' => \Illuminate\Auth\Middleware\RedirectIfAuthenticated::class,
            'shop.open' => \App\Http\Middleware\CheckShopOpen::class,
            'check.shift' => \App\Http\Middleware\CheckOpenShift::class,
        ]);

        // ✅ ใช้กับ web group เท่านั้น (ปลอดภัยสุด)
        $middleware->web(append: [
            CartMiddleware::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();