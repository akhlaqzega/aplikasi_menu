<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\MenuController as AdminMenuController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

/*
|--------------------------------------------------------------------------
| Public Routes (Akses tanpa login)
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('landing');
});

Route::get('/menu', [MenuController::class, 'index'])->name('customer.menu.index');
Route::get('/cart', [OrderController::class, 'cart'])->name('customer.cart');
Route::post('/cart/add', [OrderController::class, 'addToCart'])->name('customer.cart.add');
Route::post('/cart/update', [OrderController::class, 'updateCart'])->name('customer.cart.update');
Route::post('/cart/remove', [OrderController::class, 'removeFromCart'])->name('customer.cart.remove');
Route::get('/checkout', [OrderController::class, 'checkout'])->name('customer.checkout');
Route::post('/order', [OrderController::class, 'store'])->name('customer.order.store');
Route::get('/payment/cash/{order}', [OrderController::class, 'cashInstructions'])->name('customer.payment.cash');

Route::get('/order/clear-session', function () {
    Session::forget('pending_order_id');
    return redirect()->route('customer.menu.index');
})->name('customer.order.clear-session');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::get('/dashboard', function () {
        return redirect()->route('admin.dashboard');
    })->name('dashboard');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminOrderController::class, 'index'])->name('dashboard');
        Route::resource('menus', AdminMenuController::class);
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');
    });
});

require __DIR__.'/auth.php';