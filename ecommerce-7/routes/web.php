<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ContohController;
use App\Http\Controllers\ProductController;

Route::get('/', [HomeController::class, 'index']);

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
Route::get('/product-detail', [HomeController::class, 'productDetails']);

Route::get('/cart', function () {
    echo "Cart Page";
});

Route::get('/checkout', function () {
    echo "Checkout Page";
});
