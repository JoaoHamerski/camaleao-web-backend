<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CashFlowController;

Route::prefix('fluxo-de-caixas')->middleware('role:gerencia')->group(function () {
    Route::get('/', [
        CashFlowController::class, 'index'
    ])->name('index');

    Route::get('/get-details', [
        CashFlowController::class,
        'getDetails'
    ]);
});
