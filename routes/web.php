<?php

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
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OnlineController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UnitController;
use App\Http\Middleware\CartMiddleware;
use App\Models\Product;
use App\Models\ProductUnit;

// 👤 Guest Routes (เส้นทางสำหรับผู้เยี่ยมชม)
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'loginPage'])->name('login'); // หน้าเข้าสู่ระบบ
    Route::post('/login', [AuthController::class, 'login']); // ทำการเข้าสู่ระบบ
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request'); // ลืมรหัสผ่าน
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email'); // ส่งอีเมลรีเซ็ตรหัสผ่าน
});

Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset'); // แสดงฟอร์มรีเซ็ตรหัสผ่าน
Route::post('/reset-password', [ResetPasswordController::class, 'reset']); // รีเซ็ตรหัสผ่าน
Route::get('/online/pagenologin', [OnlineController::class, 'pagenologin'])->name('online.pagenologin');

// 🔐 Authenticated Routes (เส้นทางสำหรับผู้ที่เข้าสู่ระบบแล้ว)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout'); // ออกจากระบบ


    // 🎯 Member (เส้นทางของสมาชิก)

    Route::middleware(['role:member', CartMiddleware::class])
        ->prefix('online')
        ->name('online.')
        ->group(function () {
            Route::get('/pos', [OnlineController::class, 'index'])->name('index'); // 🛒 ร้านค้า
            Route::get('{member}/edit', [OnlineController::class, 'edit'])->name('edit'); // หน้าแก้ไขสมาชิก
            Route::put('{member}', [OnlineController::class, 'update'])->name('update'); // อัพเดทข้อมูลสมาชิก
            // 🛒 ตะกร้าสินค้า
            Route::post('/cart/add', [CartController::class, 'add'])->name('add');
            Route::get('/cart', [CartController::class, 'index'])->name('cart');
            // 💳 ชำระเงิน
            Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
            Route::post('/checkout', [OrderController::class, 'placeOrder'])->name('placeOrder');

            // 📦 ติดตามคำสั่งซื้อ
            Route::get('/track', [OrderController::class, 'trackPage'])->name('track');
            Route::post('/track', [OrderController::class, 'track'])->name('track.submit');
            Route::patch('/cart/{productId}/update', [CartController::class, 'updateQuantity'])->name('updateQuantity');
        });



    // 🎯 Admin/Staff/Owner (เส้นทางสำหรับผู้ดูแลระบบ, พนักงาน, เจ้าของ)

    Route::middleware('role:admin,staff,owner')->group(function () {
        Route::get('/sale', [SaleController::class, 'index'])->name('sale');

        // ✅ เพิ่ม endpoint สำหรับโหลดสินค้า
        Route::get('/products', function () {
            return Product::select('id', 'name', 'barcode', 'price', 'unit')->where('is_active', true)->get();
        })->name('products.json');
    });


    // เส้นทางที่เกี่ยวข้องกับการโพสต์ (สร้าง, แก้ไข, ลบ)


    // หน้าแอดมิน
    Route::get('/admin', [AdminController::class, 'index'])->name('admin');

    // เส้นทางของพนักงาน
    Route::prefix('staff')->name('staff.')->group(function () {
        Route::get('/', [StaffController::class, 'index'])->name('index'); // หน้าแสดงรายชื่อพนักงาน
        Route::get('/edit/{member}', [StaffController::class, 'edit'])->name('edit');
        Route::get('/alledit/{member}', [StaffController::class, 'alledit'])->name('alledit');
        Route::get('/create', [StaffController::class, 'create'])->name('create');  // หน้าเพิ่มพนักงาน
        Route::post('/store', [StaffController::class, 'store'])->name('store'); // บันทึกพนักงาน
        Route::put('/update/{member}', [StaffController::class, 'update'])->name('update'); // อัพเดทโพสต์
        Route::put('/allupdate/{member}', [StaffController::class, 'allupdate'])->name('allupdate'); // อัพเดทโพสต์
        Route::delete('/delete/{member}', [StaffController::class, 'destroy'])->name('delete'); // ลบโพสต์
        // Route แบบไม่ใช้ parameter
        Route::get('/audits', [StaffController::class, 'auditLogs'])->name('audits');

       Route::get('/sale', [SaleController::class, 'index']);
        Route::post('/update-stock', [SaleController::class, 'updateStockAfterSale'])->name('update.stock');
            Route::post('/sales/store', [SaleController::class, 'updateStockAfterSale'])->name('sales.store');
    Route::get('/sales/history', [SaleController::class, 'history'])->name('sales.history');
    Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');


    });

    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index'); // หน้าแสดงรายการหมวดหมู่
        Route::get('/create', [CategoryController::class, 'create'])->name('create'); // หน้าเพิ่มหมวดหมู่
        Route::post('/store', [CategoryController::class, 'store'])->name('store'); // สร้างหมวดหมู่
        Route::get('/edit/{category}', [CategoryController::class, 'edit'])->name('edit'); // หน้าแก้ไขหมวดหมู่
        Route::put('/update/{category}', [CategoryController::class, 'update'])->name('update'); // อัปเดตหมวดหมู่
        Route::delete('/delete/{category}', [CategoryController::class, 'destroy'])->name('delete'); // ลบหมวดหมู่
    });

    Route::prefix('units')->name('units.')->group(function () {
        Route::get('/', [UnitController::class, 'index'])->name('index'); // หน้าแสดงรายการหน่วยนับ
        Route::get('/create', [UnitController::class, 'create'])->name('create'); // หน้าเพิ่มหน่วยนับ
        Route::post('/store', [UnitController::class, 'store'])->name('store'); // บันทึกหน่วยนับ
        Route::get('/edit/{unit}', [UnitController::class, 'edit'])->name('edit'); // หน้าแก้ไขหน่วยนับ
        Route::put('/update/{unit}', [UnitController::class, 'update'])->name('update'); // อัปเดตหน่วยนับ
        Route::delete('/delete/{unit}', [UnitController::class, 'destroy'])->name('delete'); // ลบหน่วยนับ
        Route::get('/units/by-product', [UnitController::class, 'byProduct'])->name('byProduct');
    });

    Route::prefix('products')->name('product.')->group(function () {
        Route::resource('product', ProductController::class); // Route สำหรับ CRUD ทั่วไป

        // Route เพิ่มสินค้า
        Route::post('storeWithUnit', [ProductController::class, 'storeWithUnit'])->name('product.storeWithUnit');
        Route::put('product/{product}/update-with-unit', [ProductController::class, 'updateWithUnit'])->name('updateWithUnit');
        Route::get('/products/add-stock', [ProductController::class, 'showAddStockForm'])->name('products.add-stock-form');
        Route::post('/products/add-stock', [ProductController::class, 'storeStock'])->name('products.add-stock');
        Route::post('/products/add-stock-multi', [ProductController::class, 'addStockMulti'])->name('products.add-stock-multi');
        Route::get('/products/indexstock', [ProductController::class, 'indexstock'])->name('indexstock');
        // แก้ route นี้
        // Route ใน web.php
        Route::get('/barcode/{barcode}', function ($barcode) {
            $unit = ProductUnit::where('barcode', $barcode)->first();

            if ($unit) {
                $product = $unit->product;
                return response()->json([
                    'success' => true,
                    'product' => [
                        'id' => $product->id,
                        'name' => $product->name,
                        'unit_id' => $unit->id,
                        'unit_name' => $unit->unit_name,
                        'price' => $unit->price,
                        'wholesale' => $unit->wholesale,
                        'barcode' => $unit->barcode,
                        'quantity_per_unit' => $unit->unit_quantity, // ✅ เพิ่มตัวนี้
                    ]
                ]);
            }

            return response()->json(['success' => false]);
        });
        Route::get('/barcodes/create', function () {
            return view('barcodes.create');
        })->name('barcodes.create');

        
    });




    // เส้นทางการจัดการสมาชิก
    Route::prefix('members')->name('members.')->group(function () {
        Route::get('/index', [MemberController::class, 'index'])->name('index'); // บันทึกสมาชิก
        Route::get('/create', [MemberController::class, 'create'])->name('create'); // หน้าเพิ่มสมาชิก
        Route::post('/', [MemberController::class, 'store'])->name('store'); // บันทึกสมาชิก
        Route::get('{member}', [MemberController::class, 'show'])->name('show'); // แสดงรายละเอียดสมาชิก
        Route::get('{member}/edit', [MemberController::class, 'edit'])->name('edit'); // หน้าแก้ไขสมาชิก
        Route::put('{member}', [MemberController::class, 'update'])->name('update'); // อัพเดทข้อมูลสมาชิก
        Route::delete('{member}', [MemberController::class, 'destroy'])->name('destroy'); // ลบสมาชิก
    });
});
