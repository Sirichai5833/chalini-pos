<?php

namespace App\Http\Controllers;


use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dashboard', [
            'todaySales' => 0,
            'onlineOrders' => 0,
            'lowStockCount' => 0,
            'expiringCount' => 0,
        ]);
    }
}