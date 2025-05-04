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
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OnlineController;


// 👤 Guest Routes (เส้นทางสำหรับผู้เยี่ยมชม)
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'loginPage'])->name('login'); // หน้าเข้าสู่ระบบ
    Route::post('/login', [AuthController::class, 'login']); // ทำการเข้าสู่ระบบ
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request'); // ลืมรหัสผ่าน
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email'); // ส่งอีเมลรีเซ็ตรหัสผ่าน
});

Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset'); // แสดงฟอร์มรีเซ็ตรหัสผ่าน
Route::post('/reset-password', [ResetPasswordController::class, 'reset']); // รีเซ็ตรหัสผ่าน


// 🔐 Authenticated Routes (เส้นทางสำหรับผู้ที่เข้าสู่ระบบแล้ว)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout'); // ออกจากระบบ
    
    // 🎯 Member (เส้นทางของสมาชิก)
    Route::middleware('role:member')->name('online.')->group(function () {
        Route::get('/pos', [OnlineController::class, 'index'])->name('index'); // หน้า POS (Point of Sale) สำหรับสมาชิก
    });


    // 🎯 Admin/Staff/Owner (เส้นทางสำหรับผู้ดูแลระบบ, พนักงาน, เจ้าของ)
    Route::middleware('role:admin,staff,owner')->group(function () {
        Route::view('/sale', 'sale.sale')->name('sale'); // หน้า "sale" (การขาย)

        // เส้นทางที่เกี่ยวข้องกับการโพสต์ (สร้าง, แก้ไข, ลบ)
        Route::get('/create', [PostController::class, 'create'])->name('create'); // หน้าเพิ่มโพสต์
        Route::post('/store', [PostController::class, 'store'])->name('store'); // บันทึกโพสต์
        Route::get('/edit/{post}', [PostController::class, 'edit'])->name('edit'); // หน้าแก้ไขโพสต์
        Route::put('/update/{post}', [PostController::class, 'update'])->name('update'); // อัพเดทโพสต์
        Route::delete('/delete/{post}', [PostController::class, 'destroy'])->name('delete'); // ลบโพสต์
        Route::get('/show/{post}', [PostController::class, 'show'])->name('show'); // แสดงโพสต์

        // หน้าแอดมิน
        Route::get('/admin', [AdminController::class, 'index'])->name('admin'); 

        // เส้นทางของพนักงาน
        Route::prefix('staff')->name('staff.')->group(function () {
            Route::get('/', [StaffController::class, 'index'])->name('index'); // หน้าแสดงรายชื่อพนักงาน
            Route::get('/create', [StaffController::class, 'create'])->name('create');  // หน้าเพิ่มพนักงาน
            Route::post('/store', [StaffController::class, 'store'])->name('store'); // บันทึกพนักงาน
        });
        

        // เส้นทางการจัดการสินค้า
        Route::prefix('manage_products')->name('products.')->group(function () {
            Route::get('/', [ManageController::class, 'index'])->name('index'); // แสดงสินค้าทั้งหมด
            Route::get('/create', [ManageController::class, 'create'])->name('create'); // หน้าเพิ่มสินค้า
            Route::post('/store', [ManageController::class, 'store'])->name('store'); // บันทึกสินค้า
            Route::delete('/destroy/{id}', [ManageController::class, 'destroy'])->name('destroy'); // ลบสินค้า
            Route::put('/update{id}', [ManageController::class, 'update'])->name('update'); // อัพเดทสินค้า
            Route::get('/edit/{id}', [ManageController::class, 'edit'])->name('edit'); // หน้าแก้ไขสินค้า
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
}); 
