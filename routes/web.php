<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\OrderOwnerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OwnerDashboardController;

Route::get('/', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/owner/dashboard', [OwnerDashboardController::class, 'index'])->name('owner.dashboard');

    Route::post('/postorder', [OrderController::class, 'PostOrder'])->name('PostOrder');
    Route::resource('orders', OrderController::class);
    Route::resource('products', ProductController::class);
    Route::resource('ordersOwner', OrderOwnerController::class);

    Route::get('/cashier', [CashierController::class, 'index'])->name('cashier.index');
    Route::post('/cashier/add-item', [CashierController::class, 'addItem'])->name('cashier.addItem');
    Route::post('/cashier/complete-order', [CashierController::class, 'completeOrder'])->name('cashier.completeOrder');
    Route::post('/midtrans/callback', [CashierController::class, 'handleMidtransNotification'])->name('midtrans.callback');
    Route::get('/cashier/printReceipt/{id}', [CashierController::class, 'printReceipt'])->name('cashier.printReceipt');
    Route::get('/orders/{order}/receipt', [OrderController::class, 'receipt'])->name('orders.receipt');
});
