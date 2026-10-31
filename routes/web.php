<?php

use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\VoucherController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\WishlistController;
use App\Http\Middleware\EnsureUserIsCustomer;
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
    Route::get('/quen-mat-khau', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/quen-mat-khau', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/dat-lai-mat-khau/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/dat-lai-mat-khau', [AuthController::class, 'resetPassword'])->name('password.update');
    Route::get('/dang-ky', [AuthController::class, 'showRegister'])->name('register');
    Route::get('/dang-ky/kiem-tra', [AuthController::class, 'checkAvailability'])->name('register.availability');
    Route::post('/dang-ky', [AuthController::class, 'register']);
});

// ---- Đã đăng nhập (chung cho cả Admin và Khách) ----
Route::middleware('auth')->group(function () {
    Route::post('/dang-xuat', [AuthController::class, 'logout'])->name('logout');
    Route::get('/ho-so', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/ho-so', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/ho-so/doi-mat-khau', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

// ---- Danh sách yêu thích (chỉ khách hàng đã đăng nhập) ----
Route::middleware(['auth', EnsureUserIsCustomer::class])->prefix('yeu-thich')->name('wishlist.')->group(function () {
    Route::get('/', [WishlistController::class, 'index'])->name('index');
    Route::post('/{product}', [WishlistController::class, 'store'])->name('store');
    Route::delete('/{product}', [WishlistController::class, 'destroy'])->name('destroy');
});

// ---- Giỏ hàng (chỉ khách hàng đã đăng nhập) ----
Route::middleware(['auth', EnsureUserIsCustomer::class])
    ->prefix('gio-hang')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/', [CartController::class, 'add'])->name('add');
        Route::delete('/', [CartController::class, 'clear'])->name('clear');
        Route::patch('/{sku}', [CartController::class, 'update'])->where('sku', '[A-Za-z0-9_-]+')->name('update');
        Route::delete('/{sku}', [CartController::class, 'remove'])->where('sku', '[A-Za-z0-9_-]+')->name('remove');
    });

// ---- Đặt hàng và đơn hàng của tôi (chỉ khách hàng đã đăng nhập) ----
Route::middleware(['auth', EnsureUserIsCustomer::class])->group(function () {
    Route::get('/thanh-toan', [CheckoutController::class, 'create'])->name('checkout.create');
    Route::post('/thanh-toan', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::post('/thanh-toan/ma-giam-gia', [CheckoutController::class, 'applyVoucher'])->name('checkout.voucher.apply');
    Route::post('/thanh-toan/ma-giam-gia/xoa', [CheckoutController::class, 'removeVoucher'])->name('checkout.voucher.remove');

    Route::get('/don-hang', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/don-hang/{order}', [OrderController::class, 'show'])->whereNumber('order')->name('orders.show');
    Route::post('/don-hang/{order}/huy', [OrderController::class, 'cancel'])->whereNumber('order')->name('orders.cancel');
    Route::post('/don-hang/{order}/tra-hang', [OrderController::class, 'requestReturn'])->whereNumber('order')->name('orders.return');
    Route::get('/don-hang/{order}/hoa-don', [InvoiceController::class, 'customer'])->whereNumber('order')->name('orders.invoice');
    Route::get('/thanh-toan/{order}', [OrderController::class, 'payment'])->whereNumber('order')->name('payments.show');
    Route::post('/thanh-toan/{order}/sandbox', [OrderController::class, 'simulatePayment'])->whereNumber('order')->name('payments.simulate');
});

// ---- Khu vực quản trị (chỉ admin) ----
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [AdminOrderController::class, 'show'])->whereNumber('order')->name('orders.show');
    Route::post('orders/{order}/transition', [AdminOrderController::class, 'transition'])->whereNumber('order')->name('orders.transition');
    Route::resource('categories', CategoryController::class)->except('show');
    Route::resource('products', ProductController::class)->except('show');
    Route::delete('products/{product}/images/{image}', [ProductController::class, 'destroyImage'])
        ->whereNumber('image')
        ->name('products.images.destroy');
    Route::resource('brands', BrandController::class)->except('show');
    Route::resource('vouchers', VoucherController::class)->except('show');
    Route::get('contacts', [ContactMessageController::class, 'index'])->name('contacts.index');

    Route::get('orders/{order}/invoice', [InvoiceController::class, 'admin'])->whereNumber('order')->name('orders.invoice');

    Route::get('reports/revenue', [ReportController::class, 'revenue'])->name('reports.revenue');
    Route::get('reports/revenue/export', [ReportController::class, 'exportRevenue'])->name('reports.revenue.export');
    Route::get('reports/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');
    Route::get('reports/inventory/export', [ReportController::class, 'exportInventory'])->name('reports.inventory.export');
    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit.index');

    Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('customers/{customer}', [CustomerController::class, 'show'])->whereNumber('customer')->name('customers.show');
    Route::post('customers/{customer}/blacklist', [CustomerController::class, 'blacklist'])->whereNumber('customer')->name('customers.blacklist');
    Route::post('customers/{customer}/unblacklist', [CustomerController::class, 'unblacklist'])->whereNumber('customer')->name('customers.unblacklist');
});
