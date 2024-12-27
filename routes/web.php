<?php


use App\Http\Controllers\AccountController;
use App\Http\Controllers\AccountTypeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FromToController;
use App\Http\Controllers\PaymentTypeController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

	// Route::get('/', function () {
	// 	return Inertia::render('Welcome', [
	// 		'canLogin' => Route::has('login'),
	// 		'canRegister' => Route::has('register'),
	// 		'laravelVersion' => Application::VERSION,
	// 		'phpVersion' => PHP_VERSION,
	// 	]);
	// });
	//
	// Route::get('/dashboard', function () {
	// 	return Inertia::render('Dashboard');
	// })->middleware(['auth', 'verified'])->name('dashboard');
	//
	// Route::middleware('auth')->group(function () {
	// 	Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
	// 	Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
	// 	Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
	// });
	//
	// require __DIR__.'/auth.php';


Route::middleware(['auth'])->group(static function () {
    Route::post('user', [UserController::class, 'store']);
    Route::patch('user/{id}', [UserController::class, 'update']);
    Route::delete('user/{id}', [UserController::class, 'destroy']);
    Route::get('user/create', [UserController::class, 'create']);
    Route::get('user/{id}/edit', [UserController::class, 'edit']);
    Route::get('users', [UserController::class, 'index']);
    Route::get('users/index', [UserController::class, 'index']);


    Route::post('account_type', [AccountTypeController::class, 'store']);
    Route::patch('account_type/{id}', [AccountTypeController::class, 'update']);
    Route::delete('account_type/{id}', [AccountTypeController::class, 'destroy']);
    Route::get('account_types', [AccountTypeController::class, 'index'])->name('account_types');
    Route::get('account_types/index', [AccountTypeController::class, 'index']);
    Route::get('account_type/create', [AccountTypeController::class, 'create']);
    Route::get('account_type/{id}/edit', [AccountTypeController::class, 'edit']);


    Route::post('account', [AccountController::class, 'store']);
    Route::patch('account/{id}', [AccountController::class, 'update']);
    Route::delete('account/{id}', [AccountController::class, 'destroy']);
    Route::get('accounts', [AccountController::class, 'index'])->name('accounts');
    Route::get('accounts/index', [AccountController::class, 'index']);
    Route::get('account/create', [AccountController::class, 'create']);
    Route::get('account/{id}/edit', [AccountController::class, 'edit']);


    Route::post('category', [CategoryController::class, 'store']);
    Route::patch('category/{id}', [CategoryController::class, 'update']);
    Route::delete('category/{id}', [CategoryController::class, 'destroy']);
    Route::get('categories', [CategoryController::class, 'index'])->name('categories');
    Route::get('categories/index', [CategoryController::class, 'index']);
    Route::get('category/create', [CategoryController::class, 'create']);
    Route::get('category/{id}/edit', [CategoryController::class, 'edit']);


    Route::post('from_to', [FromToController::class, 'store']);
    Route::patch('from_to/{id}', [FromToController::class, 'update']);
    Route::delete('from_to/{id}', [FromToController::class, 'destroy']);
    Route::get('from_tos', [FromToController::class, 'index'])->name('from_tos');
    Route::get('from_tos/index', [FromToController::class, 'index']);
    Route::get('from_to/create', [FromToController::class, 'create']);
    Route::get('from_to/{id}/edit', [FromToController::class, 'edit']);


    Route::post('payment_type', [PaymentTypeController::class, 'store']);
    Route::patch('payment_type/{id}', [PaymentTypeController::class, 'update']);
    Route::delete('payment_type/{id}', [PaymentTypeController::class, 'destroy']);
    Route::get('payment_types', [PaymentTypeController::class, 'index'])->name('payment_types');
    Route::get('payment_types/index', [PaymentTypeController::class, 'index']);
    Route::get('payment_type/create', [PaymentTypeController::class, 'create']);
    Route::get('payment_type/{id}/edit', [PaymentTypeController::class, 'edit']);


    Route::post('transaction', [TransactionController::class, 'store']);
    Route::post('transaction/{transaction}', [TransactionController::class, 'changeTransactionStatus']);
    Route::patch('transaction/{id}', [TransactionController::class, 'update']);
    Route::delete('transaction/{id}', [TransactionController::class, 'destroy']);
    Route::get('transactions', [TransactionController::class, 'index'])->name('transactions');
    Route::get('transactions/index', [TransactionController::class, 'index']);
    Route::get('transaction/create', [TransactionController::class, 'create']);
    Route::get('transaction/{id}/edit', [TransactionController::class, 'edit']);

});


use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SessionsController;
use Inertia\Inertia;


Route::get('/', static function () {
	return redirect('sign-in');
})->middleware('guest');
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');
Route::get('sign-up', [RegisterController::class, 'create'])->middleware('guest')->name('register');
Route::post('sign-up', [RegisterController::class, 'store'])->middleware('guest');
Route::get('sign-in', [SessionsController::class, 'create'])->middleware('guest')->name('login');
Route::post('sign-in', [SessionsController::class, 'store'])->middleware('guest');
Route::post('verify', [SessionsController::class, 'show'])->middleware('guest');
Route::post('reset-password', [SessionsController::class, 'update'])->middleware('guest')->name('password.update');
Route::get('verify', function () {
	return view('sessions.password.verify');
})->middleware('guest')->name('verify');

Route::get('/reset-password/{token}', function ($token) {
	return view('sessions.password.reset', ['token' => $token]);
})->middleware('guest')->name('password.reset');

Route::post('sign-out', [SessionsController::class, 'destroy'])->middleware('auth')->name('logout');
Route::get('profile', [ProfileController::class, 'create'])->middleware('auth')->name('profile');
Route::post('user-profile', [ProfileController::class, 'update'])->middleware('auth');
Route::group(['middleware' => 'auth'], function () {
	Route::get('billing', function () {
		return view('pages.billing');
	})->name('billing');

	Route::get('tables', function () {
		return view('pages.tables');
	})->name('tables');

	Route::get('notifications', function () {
		return view('pages.notifications');
	})->name('notifications');

	Route::get('user-management', function () {
		return view('pages.laravel-examples.user-management');
	})->name('user-management');

	Route::get('user-profile', function () {
		return view('pages.laravel-examples.user-profile');
	})->name('user-profile');
});


Route::get('/', static function () {
	if(Auth::check()){
		return redirect('dashboard');
	}
	return Inertia::render('Auth/Login');
});

Route::get('/dashboard', static function () {
	return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(static function () {
	Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
	Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
	Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
