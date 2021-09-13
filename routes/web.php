<?php

use App\Util\Helper;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TokenController;

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

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/api/users/auth', AuthController::class);
});

Route::post('/api/sanctum/token', TokenController::class);

Route::get('/', function () {
    return view('index');
});

Helper::mapRoutes([[
    'name' => 'auth',
    'filename' => '_auth'
]]);

Route::middleware('auth')->group(function () {
    Helper::mapRoutes([
        'production',
        'users',
        'clients',
        'orders',
        'expenses',
        'cash-flow',
        'via',
        'expenses-types',
        'payments',
        'daily-cash',
        'order-notes',
        'cities',
        'branches',
        'shipping-companies',
        'clothing-types',
        'backup',
        'status',
        'financial',
        'activities',
        'production-calendar'
    ]);
});
