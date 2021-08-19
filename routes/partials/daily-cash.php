<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentsController;

Route::prefix('caixa-diario')->middleware('role:gerencia,atendimento')->group(function () {
    Route::get('/', [
        PaymentsController::class,
        'index'
    ])->name('index');

    Route::get('/payments', [
        PaymentsController::class,
        'getPaymentsOfDay'
    ]);

    Route::get('/get-total-pendencies', [
        PaymentsController::class,
        'getTotalPendencies'
    ]);

    Route::get('/get-pendencies', [
        PaymentsController::class,
        'getPendencies'
    ]);

    Route::post('/{payment}/assign-confirmation', [
        PaymentsController::class,
        'assignConfirmation'
    ]);

    Route::post('/clientes/daily-payment', [
        PaymentsController::class,
        'dailyPayment'
    ]);
});
