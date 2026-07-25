<?php

use App\Http\Controllers\Admin\StockAuditSessionController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;

// Controllers
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ManageController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OnlineController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UnitController;
use App\Http\Middleware\CartMiddleware;
use App\Http\Controllers\ManagerController;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Http\Controllers\StockAuditController;
use App\Http\Controllers\notifiController;
use App\Http\Controllers\ReportsController;
use App\Http\Livewire\LowStockProducts;
use App\Http\Controllers\StockCheckController;
use App\Http\Controllers\CreditSaleController;
use Carbon\Carbon;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ShiftController;


Route::redirect('/', '/login');


// ==========================================
// 👤 Guest Routes
// ==========================================

Route::middleware('guest')->group(function () {

    Route::get('/login', [AuthController::class, 'loginPage'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login']);


    // Forgot Password
    Route::get('/forgot-password', [
        ForgotPasswordController::class,
        'showRequestForm'
    ])->name('password.request');

    Route::post('/forgot-password', [
        ForgotPasswordController::class,
        'sendOtp'
    ])->name('password.email');


    // Verify OTP
    Route::get('/verify-otp', [
        ForgotPasswordController::class,
        'showVerifyForm'
    ])->name('password.otp.verify');

    Route::post('/verify-otp', [
        ForgotPasswordController::class,
        'verifyOtp'
    ])->name('password.otp.check');


    // Reset Password
    Route::get('/reset-password', [
        ResetPasswordController::class,
        'showResetForm'
    ])->name('password.reset');

    Route::post('/reset-password', [
        ResetPasswordController::class,
        'reset'
    ])->name('password.update');
});



// ==========================================
// 🛒 ONLINE SHOP
// ==========================================

// หน้าร้านออนไลน์
// เข้าได้ทั้ง Guest และ Member
// ไม่ต้อง Login
// ไม่ต้องเปิดกะ
Route::get('/online/pos', [
    OnlineController::class,
    'index'
])->name('online.index');


// ==========================================
// 🔐 ONLINE SHOP สำหรับสมาชิกที่ Login แล้ว
// ==========================================

Route::middleware('auth')
    ->prefix('online')
    ->name('online.')
    ->group(function () {

        // 🛒 ตะกร้าสินค้า



        // ➕ เพิ่มสินค้าลงตะกร้า

          Route::put('/cart/update-quantity/{id}', [
            OnlineController::class,
            'updateQuantity'
        ])->block(15, 15)
            ->name('updateQuantity');


        // ✏️ แก้ไขข้อมูลสมาชิก
        Route::get('/{member}/edit', [
            OnlineController::class,
            'edit'
        ])->name('edit');


        // 🔄 อัปเดตข้อมูลสมาชิก
        Route::put('/{member}', [
            OnlineController::class,
            'update'
        ])->name('update');

Route::patch(
    '/cart/update/{id}',
    [OnlineController::class, 'updateQuantity']
)->block(15, 15)
    ->name('online.updateQuantity');

 // 💳 สร้าง QR พร้อมเพย์
        Route::get('/generate-qr', [
            OrderController::class,
            'generateQRCode'
        ])->name('generate.qr');

Route::delete(
    '/cart/remove/{id}',
    [OnlineController::class, 'remove']
)->block(15, 15)
    ->name('online.remove');

        // ==========================================
        // 💳 CHECKOUT
        // ใช้ OrderController
        // ==========================================
        
        Route::get('/checkout', [
    OnlineController::class,
    'showCheckoutForm'
])->name('checkout');

Route::post('/checkout', [
    OnlineController::class,
    'processCheckout'
])->block(15, 15)
    ->name('checkout.store');


        // ==========================================
        // 📦 ติดตามคำสั่งซื้อ
        // ==========================================

        Route::get('/track', [
            OrderController::class,
            'track'
        ])->name('track');

    });

// ==========================================
// 🔐 Authenticated Routes
// ==========================================

Route::middleware('auth')->group(function () {

// ==========================================
// Logout
// ==========================================

Route::post('/logout', [
    AuthController::class,
    'logout'
])->name('logout');


// ==========================================
// SHIFT
// เปิดกะ / ปิดกะ
// ==========================================

Route::middleware('role:admin,manager,owner,staff')->group(function () {
    Route::get('/shift/open', [
        ShiftController::class,
        'create'
    ])->name('shift.open');

    Route::post('/shift/open', [
        ShiftController::class,
        'store'
    ])->name('shift.store');

    Route::get('/shift/close', [
        ShiftController::class,
        'close'
    ])->name('shift.close');

    Route::post('/shift/close', [
        ShiftController::class,
        'update'
    ])->name('shift.update');
});


// ==========================================
// 🛒 POS หน้าร้าน
// เฉพาะหน้าขายหน้าร้านเท่านั้น
// ต้อง Login + เปิดร้าน + เปิดกะ
// ==========================================

Route::middleware([
    'role:admin,manager,owner,staff',
    'shop.open',
    'check.shift'
])->group(function () {

    Route::get('/sale', [
        SaleController::class,
        'index'
    ])->name('sale');

});

Route::get(
    '/manager/dashboard',
    [ManagerController::class, 'dashboard']
)->middleware('role:admin,manager,owner')
    ->name('manager.dashboard');


// ==========================================
// 🛍️ ONLINE SHOP
// สมาชิกที่ Login แล้ว
// ==========================================

Route::middleware(['auth', CartMiddleware::class])
    ->prefix('online')
    ->name('online.')
    ->group(function () {


        // ตะกร้าสินค้า
        Route::get('/cart', [
            OnlineController::class,
            'cart'
        ])->name('cart');


        // เพิ่มสินค้าเข้าตะกร้า
        Route::post('/cart/add', [
            OnlineController::class,
            'add'
        ])->block(15, 15)
            ->name('add');



        // แก้ไขจำนวนสินค้า


        // อัปเดตสินค้าในตะกร้า

        // อัปเดตจำนวนสินค้าในตะกร้า

    });


// ==========================================
// 👨‍💼 Admin / Staff / Owner
// ==========================================

Route::middleware('role:admin,manager,staff,owner')->group(function () {


    // ==========================================
    // Products JSON
    // ==========================================

    Route::get('/products', function () {

        return Product::select(
            'id',
            'name',
            'barcode',
            'price',
            'unit'
        )
            ->where('is_active', true)
            ->get();

    })->name('products.json');


    // ==========================================
    // Staff Management
    // ==========================================

    Route::prefix('staff')
        ->name('staff.')
        ->group(function () {

            Route::get('/', [
                StaffController::class,
                'index'
            ])->name('index');

            Route::get('/edit/{member}', [
                StaffController::class,
                'edit'
            ])->name('edit');

            Route::get('/alledit/{member}', [
                StaffController::class,
                'alledit'
            ])->name('alledit');

            Route::get('/create', [
                StaffController::class,
                'create'
            ])->name('create');

            Route::post('/store', [
                StaffController::class,
                'store'
            ])->name('store');

            Route::put('/update/{member}', [
                StaffController::class,
                'update'
            ])->name('update');

            Route::put('/allupdate/{member}', [
                StaffController::class,
                'allupdate'
            ])->name('allupdate');

            Route::delete('/delete/{member}', [
                StaffController::class,
                'destroy'
            ])->name('delete');


            // ==========================================
            // Stock Check
            // ==========================================

            Route::get(
                'stock-check',
                [StockCheckController::class, 'index']
            )->name('stock.check.index');

            Route::get(
                'stock-check-report',
                [StockCheckController::class, 'report']
            )->name('stock.check.report');

            Route::get(
                'stock-check/{check}',
                [StockCheckController::class, 'detail']
            )->name('stock.check.detail');

            Route::post(
                'stock-check',
                [StockCheckController::class, 'store']
            )->name('stock.check.store');


            // ==========================================
            // Audit
            // ==========================================

            Route::get('/audits', [
                StaffController::class,
                'auditLogs'
            ])->name('audits');


            // ==========================================
            // Sales History
            // ==========================================

            Route::get('/sales/history', [
                SaleController::class,
                'history'
            ])->name('sales.history');


            // ==========================================
            // Credit Sales
            // ==========================================

            Route::post('/credit-sale', [
                SaleController::class,
                'creditSale'
            ]);

            Route::get('/staff/credit-sales/{id}', [
                CreditSaleController::class,
                'show'
            ])->name('credit.sales.show');

            Route::get('/credit-sales/customer/{id}', [
                CreditSaleController::class,
                'customerBills'
            ])->name('credit.customer');

            Route::post('/staff/credit/pay-bill/{id}', [
                CreditSaleController::class,
                'payBill'
            ])->name('credit.pay.bill');

            Route::post('/staff/credit/pay-selected/{id}', [
                CreditSaleController::class,
                'paySelected'
            ])->name('credit.pay.selected');

            Route::post('/credit-sales/{id}/pay', [
                CreditSaleController::class,
                'pay'
            ])->name('credit.sales.pay');

            Route::get('/credit-sales', [
                CreditSaleController::class,
                'index'
            ])->name('credit.sales.index');

            Route::post('/credit-sales/pay-all/{id}', [
                CreditSaleController::class,
                'payAll'
            ])->name('credit.pay.all');

            Route::resource(
                'sales',
                SaleController::class
            )->only([
                'index',
                'show',
                'destroy',
            ]);

        });

});


// ==========================================
// Admin Dashboard
// ==========================================

Route::get('/admin', [
    AdminController::class,
    'index'
])->middleware('role:admin')
    ->name('admin');


// ==========================================
// Shop Settings
// ==========================================

Route::get('/staff/settings', [
    SettingController::class,
    'index'
])
    ->middleware('role:admin,manager,staff,owner')
    ->name('staff.settings');

Route::put('/staff/settings', [
    SettingController::class,
    'update'
])
    ->middleware('role:admin')
    ->name('staff.settings.update');


// ==========================================
// Orders / History
// ==========================================

Route::middleware('role:admin,manager,staff,owner')->group(function () {

Route::get('/orders', [
    OrderController::class,
    'ordersList'
])->name('orders.list');

Route::get('/orders/history', [
    OrderController::class,
    'orderHistory'
])->name('orders.history');

Route::get('/product/movement-history', [
    ProductController::class,
    'movementHistory'
])->name('product.movement-history');


// ==========================================
// Customer
// ==========================================

Route::post('/customers/{id}/suspend', [
    CustomerController::class,
    'suspend'
]);

Route::post('/customers/{id}/restore', [
    CustomerController::class,
    'restore'
]);


// ==========================================
// Update Stock
// ==========================================

Route::post('/update-stock', [
    SaleController::class,
    'updateStockAfterSale'
])->name('update.stock');


// ==========================================
// Categories
// ==========================================

Route::prefix('categories')
    ->name('categories.')
    ->group(function () {

        Route::get('/', [
            CategoryController::class,
            'index'
        ])->name('index');

        Route::get('/create', [
            CategoryController::class,
            'create'
        ])->name('create');

        Route::post('/store', [
            CategoryController::class,
            'store'
        ])->name('store');

        Route::get('/edit/{category}', [
            CategoryController::class,
            'edit'
        ])->name('edit');

        Route::put('/update/{category}', [
            CategoryController::class,
            'update'
        ])->name('update');

        Route::delete('/delete/{category}', [
            CategoryController::class,
            'destroy'
        ])->name('delete');

    });


// ==========================================
// Orders
// ==========================================

Route::post('/orders/{id}/accept', [
    OrderController::class,
    'acceptOrder'
])->name('orders.accept');

Route::get('/my-orders', [
    OrderController::class,
    'myOrders'
])->name('orders.my');

Route::get('/orders/my-accepted', [
    OrderController::class,
    'myAcceptedOrders'
])->name('orders.myAccepted');

Route::patch('/orders/{order}/status', [
    OrderController::class,
    'updateStatus'
])->name('orders.updateStatus');

Route::get('/orders/{id}', [
    OrderController::class,
    'show'
])->whereNumber('id')
    ->name('orders.show');



// ==========================================
// Barcode
// ==========================================

Route::get('/barcode/check', [
    ProductController::class,
    'checkBarcode'
])->name('barcode.check');

Route::get('/check-product-name', [
    ProductController::class,
    'checkProductName'
])->name('product.name.check');

Route::get('/check-barcode', [
    ProductController::class,
    'checkBarcode'
])->name('barcode.verify');


// ==========================================
// Units
// ==========================================

Route::prefix('units')
    ->name('units.')
    ->group(function () {

        Route::get('/', [
            UnitController::class,
            'index'
        ])->name('index');

        Route::get('/create', [
            UnitController::class,
            'create'
        ])->name('create');

        Route::post('/store', [
            UnitController::class,
            'store'
        ])->name('store');

        Route::get('/edit/{unit}', [
            UnitController::class,
            'edit'
        ])->name('edit');

        Route::put('/update/{unit}', [
            UnitController::class,
            'update'
        ])->name('update');

        Route::delete('/delete/{unit}', [
            UnitController::class,
            'destroy'
        ])->name('delete');

        Route::get('/units/by-product', [
            UnitController::class,
            'byProduct'
        ])->name('byProduct');

    });


// ==========================================
// Reports
// ==========================================

Route::prefix('reports')
    ->name('reports.')
    ->group(function () {

        Route::get('/daily', [
            ReportsController::class,
            'index'
        ])->name('daily');

    });


// ==========================================
// Products
// ==========================================

Route::prefix('products')
    ->name('product.')
    ->group(function () {

        Route::resource(
            'product',
            ProductController::class
        )->only([
            'index',
            'create',
            'show',
            'edit',
            'destroy',
        ]);

        Route::post(
            'storeWithUnit',
            [ProductController::class, 'storeWithUnit']
        )->name('product.storeWithUnit');

        Route::put(
            'product/{product}/update-with-unit',
            [ProductController::class, 'updateWithUnit']
        )->name('updateWithUnit');

        Route::get(
            '/products/add-stock',
            [ProductController::class, 'showAddStockForm']
        )->name('products.add-stock-form');

        Route::post(
            '/products/add-stock',
            [ProductController::class, 'storeStock']
        )->name('products.add-stock');

        Route::post(
            '/products/add-stock-multi',
            [ProductController::class, 'addStockMulti']
        )->name('products.add-stock-multi');

        Route::get(
            '/products/indexstock',
            [ProductController::class, 'indexstock']
        )->name('indexstock');

        Route::delete(
            'product/image/{id}',
            [ProductController::class, 'deleteImage']
        )->name('product.image.delete');

        Route::get(
            '/barcode/{barcode}',
            [BarcodeController::class, 'showByBarcode']
        )->name('barcode.lookup');

        Route::get(
            '/barcodes',
            [BarcodeController::class, 'index']
        )->name('barcodes.index');

        Route::get(
            '/products/history',
            [ProductController::class, 'allHistory']
        )->name('products.allHistory');

        Route::get(
            '/products/move-to-store',
            [StockController::class, 'formMoveToStore']
        )->name('stock.to.store');

        Route::post(
            '/products/move-to-moveToStore',
            [StockController::class, 'moveToStore']
        )->name('stock.to.frontstore');

        Route::get(
            '/stock-in-history',
            [ProductController::class,
            'searchStockInHistory'
        ])->name('stock-in-history');

    });


// ==========================================
// Notifications
// ==========================================

Route::get('/notifications/low-stock', [
    notifiController::class,
    'lowStock'
])->name('notification.OutStock');

Route::get('/notifications/near-expiry', [
    notifiController::class,
    'nearExpiry'
])->name('notification.expire');

Route::post('/products/expiry/{id}/acknowledge', [
    notifiController::class,
    'acknowledgeExpiry'
])->name('notification.expiry.acknowledge');


// ==========================================
// Members Management
// ==========================================

Route::prefix('members')
    ->name('members.')
    ->group(function () {

        Route::get('/index', [
            MemberController::class,
            'index'
        ])->name('index');

        Route::get('/create', [
            MemberController::class,
            'create'
        ])->name('create');

        Route::post('/members/set-floors', [
            MemberController::class,
            'setFloors'
        ])->name('members.setFloors');

        Route::post('/', [
            MemberController::class,
            'store'
        ])->name('store');

        Route::get('{member}', [
            MemberController::class,
            'show'
        ])->name('show');

        Route::get('{member}/edit', [
            MemberController::class,
            'edit'
        ])->name('edit');

        Route::put('{member}', [
            MemberController::class,
            'update'
        ])->name('update');

        Route::delete('{member}', [
            MemberController::class,
            'destroy'
        ])->name('destroy');

        Route::post('/customers', [
            CustomerController::class,
            'store'
        ]);

    });

});

});
