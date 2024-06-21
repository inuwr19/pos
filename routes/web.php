<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::post('/postorder', [OrderController::class, 'PostOrder'])->name('PostOrder');
    Route::resource('orders', OrderController::class);
    Route::resource('products', ProductController::class);
    // Route::get('/product', [ProductController::class, 'productIndex'])->name('productIndex');
    Route::get('/cashier', [CashierController::class, 'index'])->name('cashier.index');
    Route::post('/cashier/add-item', [CashierController::class, 'addItem'])->name('cashier.addItem');
    Route::post('/cashier/complete-order', [CashierController::class, 'completeOrder'])->name('cashier.completeOrder');
    Route::post('/midtrans/callback', [CashierController::class, 'handleMidtransNotification'])->name('midtrans.callback');
});





