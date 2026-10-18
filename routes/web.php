<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

// ---- Trang công khai ----
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/san-pham', [ShopController::class, 'index'])->name('products.index');
Route::get('/san-pham/{product}', [ShopController::class, 'show'])
    ->where('product', '[A-Za-z0-9_-]+')
    ->name('products.show');

// Liên hệ (giới hạn 5 lần gửi mỗi phút để chống spam)
Route::get('/lien-he', [ContactController::class, 'show'])->name('contact');
Route::post('/lien-he', [ContactController::class, 'store'])->middleware('throttle:5,1')->name('contact.store');

// ---- Khách (chưa đăng nhập) ----
Route::middleware('guest')->group(function () {
    Route::get('/dang-nhap', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/dang-nhap', [AuthController::class, 'login']);
    Route::get('/dang-ky', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/dang-ky', [AuthController::class, 'register']);
});

// ---- Đã đăng nhập ----
Route::middleware('auth')->group(function () {
    Route::post('/dang-xuat', [AuthController::class, 'logout'])->name('logout');
});

// ---- Khu vực quản trị (chỉ admin) ----
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('categories', CategoryController::class)->except('show');
    Route::resource('products', ProductController::class)->except('show');
    Route::get('contacts', [ContactMessageController::class, 'index'])->name('contacts.index');
});
