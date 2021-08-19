<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CookieController;

Route::post('/set-cookie', [CookieController::class, 'setCookie']);
Route::delete('/set-cookie', [CookieController::class, 'deleteCookie']);
