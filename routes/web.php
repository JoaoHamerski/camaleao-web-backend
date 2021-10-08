<?php

use App\Util\Helper;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\OrdersController;

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

Route::get('/', function () {
    return response('', 200);
});

// Route for testing
Route::get('/resource', function () {
    return new \App\Http\Resources\OrderResource(App\Models\Order::first());
});

Route::post('/login', [LoginController::class, 'login'])->name('login');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

Route::middleware('auth')->prefix('api')->group(function () {
    Route::get('/users/auth', AuthController::class);
});

Route::post('/api/sanctum/token', TokenController::class);

Route::prefix('api/clients')->name('clients.')->group(function () {
    Route::get('/', [ClientsController::class, 'index'])->name('index');
    Route::get('/{client}', [ClientsController::class, 'show'])->name('show');
    Route::get('/{client}/orders', [ClientsController::class, 'orders'])->name('orders');
});

Route::prefix('api/orders')->name('orders')->group(function () {
    Route::get('/orders', [OrdersController::class, 'index'])->name('index');
    Route::get('/orders/{client}', [OrdersController::class, 'show'])->name('show');
});
