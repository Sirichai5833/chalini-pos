<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CartMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'guest' => \Illuminate\Auth\Middleware\RedirectIfAuthenticated::class,
        ]);

        // 👇 เพิ่มตรงนี้ เพื่อให้ middleware ทำงาน global
        $middleware->append(CartMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create(); // <- ตรงนี้ต้องมี ; ปิดท้าย

