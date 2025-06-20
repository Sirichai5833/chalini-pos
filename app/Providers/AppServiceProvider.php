<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;
namespace App\Providers;

use App\Models\Order;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\View;
use App\Models\Product;
use App\Models\ProductBatch;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap(); // ใช้ Bootstrap pagination style

        // 👇 เพิ่ม Activity log binding ตรงนี้
        Activity::saving(function (Activity $activity) {
            if (Auth::check()) {
                $activity->causer_id = Auth::id();
                $activity->causer_type = \App\Models\User::class;
            }
        });

         View::composer('*', function ($view) {
        // สินค้าใกล้หมด
        $lowStockCount = Product::with('stock')->get()
            ->filter(fn ($product) =>
                ($product->stock->warehouse_stock ?? 0) < 10 ||
                ($product->stock->store_stock ?? 0) < 5
            )->count();

        // สินค้าใกล้หมดอายุ (ใน 30 วัน)
        $expireCount = ProductBatch::whereDate('expiry_date', '<=', now()->addDays(30))->count();

        $view->with('lowStockCount', $lowStockCount)
             ->with('expireCount', $expireCount);
    });
     View::composer('*', function ($view) {
        $view->with('pendingOrderCount', Order::where('status', 'pending')->count());
    });

    }
}
