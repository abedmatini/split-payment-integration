<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentReturnController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Public: product listing (no auth required to browse)
Route::get('/products', [ProductController::class, 'index'])->name('products.index');

// Cart (auth required to add/view/update/remove)
Route::middleware('auth')->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
    Route::patch('/cart/{cart}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cart}', [CartController::class, 'destroy'])->name('cart.destroy');
    // Checkout (Phase 8)
    Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/payment/{order}', [CheckoutController::class, 'paymentRedirect'])->name('checkout.payment');
    // PayFast redirects the user here after payment; status is set automatically by ITN (webhook)
    Route::get('/payment/return', PaymentReturnController::class)->name('payment.return');
    Route::get('/payment/cancel', function () {
        return redirect()->route('dashboard')->with('status', __('Payment was cancelled.'));
    })->name('payment.cancel');
    // Order history (Phase 12)
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/mark-paid', [OrderController::class, 'markPaid'])->name('orders.mark-paid');
    Route::patch('/orders/{order}/complete', [OrderController::class, 'complete'])->name('orders.complete');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
