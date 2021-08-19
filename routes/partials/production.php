<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductionController;

Route::middleware('role:gerencia')->group(function () {
    Route::get('/lista-de-producao', [
        ProductionController::class,
        'indexAdmin'
    ])->name('indexAdmin');
});

Route::middleware('role:costura,estampa')->prefix('producao')->group(function () {
    Route::get('/', [
        ProductionController::class,
        'index'
    ])->name('home');

    Route::get('/get-commissions', [
        ProductionController::class,
        'getCommissions'
    ]);

    Route::post('/{commissionUser}/confirm', [
        ProductionController::class,
        'assignConfirmation'
    ]);
});

Route::middleware('role:gerencia,costura,estampa')->group(function () {
    Route::get('/producao/comissao-do-mes', [
        ProductionController::class,
        'calculateMonthCommission'
    ]);
});
