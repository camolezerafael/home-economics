<?php


use App\Http\Controllers\UserController;

use App\Http\Controllers\AccountTypeController;

use App\Http\Controllers\AccountController;

use App\Http\Controllers\CategoryController;

use App\Http\Controllers\FromToController;

use App\Http\Controllers\PaymentTypeController;

use App\Http\Controllers\TransactionController;

use App\Http\Controllers\TransferController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix('v1/admin')->middleware(['auth'])->group(function () {
    Route::get('user', [UserController::class, 'index']);
    Route::get('user/{id}', [UserController::class, 'show']);
    Route::post('user', [UserController::class, 'store']);
    Route::put('user/create', [UserController::class, 'create']);
    Route::patch('user/{id}', [UserController::class, 'update']);
    Route::post('user/{id}/edit', [UserController::class, 'edit']);
    Route::delete('user/{id}', [UserController::class, 'destroy']);
});

Route::prefix('v1')->middleware(['auth'])->group(function () {
    Route::get('account_type', [AccountTypeController::class, 'index']);
    Route::get('account_type/{id}', [AccountTypeController::class, 'show']);
    Route::post('account_type', [AccountTypeController::class, 'store']);
    Route::put('account_type/create', [AccountTypeController::class, 'create']);
    Route::patch('account_type/{id}', [AccountTypeController::class, 'update']);
    Route::post('account_type/{id}/edit', [AccountTypeController::class, 'edit']);
    Route::delete('account_type/{id}', [AccountTypeController::class, 'destroy']);
});

Route::prefix('v1')->middleware(['auth'])->group(function () {
    Route::get('account', [AccountController::class, 'index']);
    Route::get('account/{id}', [AccountController::class, 'show']);
    Route::post('account', [AccountController::class, 'store']);
    Route::put('account/create', [AccountController::class, 'create']);
    Route::patch('account/{id}', [AccountController::class, 'update']);
    Route::post('account/{id}/edit', [AccountController::class, 'edit']);
    Route::delete('account/{id}', [AccountController::class, 'destroy']);
});

Route::prefix('v1')->middleware(['auth'])->group(function () {
    Route::get('category', [CategoryController::class, 'index']);
    Route::get('category/{id}', [CategoryController::class, 'show']);
    Route::post('category', [CategoryController::class, 'store']);
    Route::put('category/create', [CategoryController::class, 'create']);
    Route::patch('category/{id}', [CategoryController::class, 'update']);
    Route::post('category/{id}/edit', [CategoryController::class, 'edit']);
    Route::delete('category/{id}', [CategoryController::class, 'destroy']);
});

Route::prefix('v1')->middleware(['auth'])->group(function () {
    Route::get('from_to', [FromToController::class, 'index']);
    Route::get('from_to/{id}', [FromToController::class, 'show']);
    Route::post('from_to', [FromToController::class, 'store']);
    Route::put('from_to/create', [FromToController::class, 'create']);
    Route::patch('from_to/{id}', [FromToController::class, 'update']);
    Route::post('from_to/{id}/edit', [FromToController::class, 'edit']);
    Route::delete('from_to/{id}', [FromToController::class, 'destroy']);
});

Route::prefix('v1')->middleware(['auth'])->group(function () {
    Route::get('payment_type', [PaymentTypeController::class, 'index']);
    Route::get('payment_type/{id}', [PaymentTypeController::class, 'show']);
    Route::post('payment_type', [PaymentTypeController::class, 'store']);
    Route::put('payment_type/create', [PaymentTypeController::class, 'create']);
    Route::patch('payment_type/{id}', [PaymentTypeController::class, 'update']);
    Route::post('payment_type/{id}/edit', [PaymentTypeController::class, 'edit']);
    Route::delete('payment_type/{id}', [PaymentTypeController::class, 'destroy']);
});

Route::prefix('v1')->middleware(['auth'])->group(function () {
    Route::get('transactions', [TransactionController::class, 'index']);
    Route::get('transaction/{id}', [TransactionController::class, 'show']);
    Route::post('transaction', [TransactionController::class, 'store']);
    Route::put('transaction/create', [TransactionController::class, 'create']);
    Route::patch('transaction/{id}', [TransactionController::class, 'update']);
    Route::post('transaction/{id}/edit', [TransactionController::class, 'edit']);
    Route::delete('transaction/{id}', [TransactionController::class, 'destroy']);
});

Route::prefix('v1')->middleware(['auth'])->group(function () {
    Route::get('transfer', [TransferController::class, 'index']);
    Route::get('transfer/{id}', [TransferController::class, 'show']);
    Route::post('transfer', [TransferController::class, 'store']);
    Route::put('transfer/create', [TransferController::class, 'create']);
    Route::patch('transfer/{id}', [TransferController::class, 'update']);
    Route::post('transfer/{id}/edit', [TransferController::class, 'edit']);
    Route::delete('transfer/{id}', [TransferController::class, 'destroy']);
});

