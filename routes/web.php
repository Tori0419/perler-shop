<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\UploadController;
use App\Http\Middleware\AdminAuthenticate;
use App\Http\Middleware\CustomerAuthenticate;
use Illuminate\Support\Facades\Route;

Route::get('/uploads/products/{filename}', [UploadController::class, 'productImage'])
    ->where('filename', '[A-Za-z0-9\-\._]+')
    ->name('uploads.product-image');

Route::get('/login', [CustomerAuthController::class, 'showLogin'])->name('customer.login');
Route::post('/login', [CustomerAuthController::class, 'login'])->name('customer.login.submit');
Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('customer.logout');

Route::middleware(CustomerAuthenticate::class)
    ->group(function (): void {
        Route::get('/', [ShopController::class, 'index'])->name('shop.index');
        Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
        Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
        Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
        Route::post('/cart/update-ajax', [CartController::class, 'updateAjax'])->name('cart.update.ajax');
        Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');

        Route::post('/checkout', [OrderController::class, 'checkout'])->name('checkout.submit');
        Route::get('/orders/history', [OrderController::class, 'history'])->name('orders.history');
    });

Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

Route::prefix('admin')
    ->middleware(AdminAuthenticate::class)
    ->group(function (): void {
        Route::get('/products', [AdminProductController::class, 'index'])->name('admin.products.index');
        Route::post('/products', [AdminProductController::class, 'store'])->name('admin.products.store');
        Route::put('/products/{id}', [AdminProductController::class, 'update'])->name('admin.products.update');
        Route::delete('/products/{id}', [AdminProductController::class, 'destroy'])->name('admin.products.destroy');
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('admin.orders.index');
        Route::post('/orders/{id}/ship', [AdminOrderController::class, 'ship'])->name('admin.orders.ship');
        Route::post('/orders/{id}/cancel', [AdminOrderController::class, 'cancel'])->name('admin.orders.cancel');
    });
