<?php

use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ContohController;
use App\Http\Controllers\ProductController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');

Route::get('/contoh', [ContohController::class, 'index']);
Route::get('/contoh2', [ContohController::class, 'contoh2']);

Route::prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        echo "Admin Dashboard";
    });
    Route::resource('products', ProductController::class);
});

Route::get('/products', function () {
    return view('product');
});
Route::get('/product-detail/{slug}', [HomeController::class, 'productDetails'])->name('product.detail');

Route::get('/checkout', function () {
    echo "Checkout Page";
});
