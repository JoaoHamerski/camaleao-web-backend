<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FinancialController;

Route::middleware('role:gerencia')->group(function () {
    Route::get('/financeiro', [
        FinancialController::class, 'index'
    ])->name('index');
});
