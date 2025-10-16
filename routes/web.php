<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\MenuController as AdminMenuController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Models\Order;
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

// Route untuk Daftar Menu (Menggunakan MenuController)
Route::get('/menu', [MenuController::class, 'index'])->name('customer.menu.index');

// Grouping OrderController routes for customer interactions (Cart, Checkout, Payment)
Route::controller(OrderController::class)->group(function () {
    // Cart & Order Management
    Route::get('/cart', 'cart')->name('customer.cart');
    Route::post('/cart/add', 'addToCart')->name('customer.cart.add');
    Route::post('/cart/update', 'updateCart')->name('customer.cart.update');
    Route::post('/cart/remove', 'removeFromCart')->name('customer.cart.remove');

    // Checkout & Store
    Route::get('/checkout', 'checkout')->name('customer.checkout');
    Route::post('/order', 'store')->name('customer.order.store');

    // Payment & Confirmation Pages
    Route::get('/payment/cash/{order}', 'cashInstructions')->name('customer.payment.cash');
    Route::get('/order/confirmed/{order}', function (Order $order) {
        return view('customer.order-confirmed', compact('order'));
    })->name('customer.order.confirmed');

    // Tracking & Utilities
    Route::get('/track', 'showTrackForm')->name('customer.track.index');
    Route::post('/track', 'trackOrder')->name('customer.track.order');
    Route::get('/order/clear-session', function () {
        Session::forget('pending_order_id');
        return redirect()->route('customer.menu.index');
    })->name('customer.order.clear-session');
});


/*
|--------------------------------------------------------------------------
| Authenticated Routes (Admin/Kasir)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard', function () {
        // Redirect dari route dashboard Breeze ke dashboard admin kita
        return redirect()->route('admin.dashboard');
    })->name('dashboard');

    Route::prefix('admin')->name('admin.')->group(function () {
        // Dashboard Umum (Statistik Ringkas)
        Route::get('/dashboard', [AdminOrderController::class, 'dashboard'])->name('dashboard');

        // Kelola Pesanan Masuk (Tabel Detail)
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('orders/history', [AdminOrderController::class, 'history'])->name('orders.history'); // BARU
        Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');

        Route::get('/orders/check-new', [AdminOrderController::class, 'checkNewOrders'])->name('orders.checkNew');

        // Data Master CRUD
        Route::resource('menus', AdminMenuController::class);
        Route::resource('categories', CategoryController::class);
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');

        Route::resource('tables', TableController::class); // <<< Untuk crud meja
    });
});

require __DIR__ . '/auth.php';
