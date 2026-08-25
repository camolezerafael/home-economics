<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AccountTypeController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SessionsController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FromToController;
use App\Http\Controllers\PaymentTypeController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return Inertia::render('Auth/Login');
})->middleware('guest')->name('home');

Route::middleware('guest')->group(function () {
    Route::get('sign-up', [RegisterController::class, 'create'])->name('register');
    Route::post('sign-up', [RegisterController::class, 'store']);

    Route::get('sign-in', [SessionsController::class, 'create'])->name('login');
    Route::post('sign-in', [SessionsController::class, 'store'])->middleware('throttle:5,1');

    Route::post('verify', [SessionsController::class, 'show'])->middleware('throttle:5,1');
    Route::get('forgot-password', fn () => Inertia::render('Auth/ForgotPassword'))->name('password.request');
    Route::post('forgot-password', [SessionsController::class, 'show'])->middleware('throttle:5,1');

    Route::get('reset-password/{token}', fn ($token) => Inertia::render('Auth/ResetPassword', ['token' => $token]))->name('password.reset');
    Route::post('reset-password', [SessionsController::class, 'update'])->middleware('throttle:5,1')->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('sign-out', [SessionsController::class, 'destroy'])->name('logout');

    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('user', UserController::class)->parameters(['user' => 'user'])->names('user');

    Route::resource('account_type', AccountTypeController::class)->parameters(['account_type' => 'account_type'])->names('account_type');
    Route::resource('account', AccountController::class)->parameters(['account' => 'account'])->names('account');
    Route::resource('category', CategoryController::class)->parameters(['category' => 'category'])->names('category');
    Route::resource('from_to', FromToController::class)->parameters(['from_to' => 'from_to'])->names('from_to');
    Route::resource('payment_type', PaymentTypeController::class)->parameters(['payment_type' => 'payment_type'])->names('payment_type');
    Route::resource('transaction', TransactionController::class)->parameters(['transaction' => 'transaction'])->names('transaction');

    Route::post('transaction/{transaction}/status', [TransactionController::class, 'changeTransactionStatus'])->name('transaction.status');
});
