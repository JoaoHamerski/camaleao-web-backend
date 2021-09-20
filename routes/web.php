<?php

use App\Util\Helper;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\Auth\LoginController;

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

Route::get('/resource', function () {
    return \App\Http\Resources\ClientResource::collection(App\Models\Client::all());
});

Route::post('/login', [LoginController::class, 'login'])->name('login');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

Route::middleware('auth')->prefix('api')->group(function () {
    Route::get('/users/auth', AuthController::class);
});

Route::prefix('api')->group(function () {
    Route::prefix('clients')->group(function () {
        Route::get('/', [ClientsController::class, 'index']);
    });
});

Route::post('/api/sanctum/token', TokenController::class);
