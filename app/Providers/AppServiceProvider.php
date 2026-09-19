<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;
use Spatie\Activitylog\Models\Activity;

use App\Models\ProductStocks;
use App\Models\ProductBatch;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {

     $shopName = null;

     if (!app()->runningInConsole()) {
         $shopName = cache()->remember('shop_name', 60 * 60, function () {
             return Schema::hasTable('settings')
                 ? DB::table('settings')->where('key', 'shop_name')->value('value')
                 : null;
         });
     }

     $shopName ??= 'ชาลินี';

    View::share('shopName', $shopName);
    
        Paginator::useBootstrap();

        URL::forceScheme('https');

        Activity::saving(function (Activity $activity) {
            if (Auth::check()) {
                $activity->causer_id = Auth::id();
                $activity->causer_type = \App\Models\User::class;
            }
        });

        View::composer(['layouts.*', 'dashboard.*'], function ($view) {
            if (!Auth::check()) {
                return;
            }

            $lowStockCount = cache()->remember('dashboard.low_stock_count', 60, function () {
                return ProductStocks::query()
                    ->join('products', 'products.id', '=', 'product_stocks.product_id')
                    ->whereRaw('(COALESCE(product_stocks.store_stock, 0) + COALESCE(product_stocks.warehouse_stock, 0)) <= COALESCE(products.min_stock, 0)')
                    ->distinct('product_stocks.product_id')
                    ->count('product_stocks.product_id');
            });

            $expireCount = cache()->remember('dashboard.expire_count', 60, function () {
                return ProductBatch::query()
                    ->where('is_acknowledged', false)
                    ->whereDate('expiry_date', '<=', now()->addDays(30))
                    ->count();
            });

            $view->with([
                'lowStockCount' => $lowStockCount,
                'expireCount' => $expireCount,
            ]);
        });

        View::composer('*', function ($view) {
            $keys = [
                'shop_name',
                'phone',
                'address',
                'promptpay',
                'account_name',
                'qr_image',
                'receipt_footer',
                'receipt_size',
                'stock_warning',
            ];

            $shopSettings = cache()->remember('shop_settings', 60 * 60, function () use ($keys) {
                $settings = Setting::query()
                    ->whereIn('key', $keys)
                    ->pluck('value', 'key')
                    ->map(function ($value) {
                        $decoded = json_decode($value, true);
                        return $decoded === null ? $value : $decoded;
                    })
                    ->toArray();

                $defaults = array_combine($keys, array_fill(0, count($keys), ''));
                return (object) array_merge($defaults, $settings);
            });

            $view->with('shopSettings', $shopSettings);
        });
    }
}
