<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentsController;

Route::prefix('cliente')->middleware('role:gerencia,atendimento')->group(function () {
    Route::post('/cliente/{client}/pedido/{order}/new-payment', [
        PaymentsController::class,
        'store'
    ])->name('store');

    Route::get('/cliente/{client}/pedido/{order}/pagamento/{payment}/get-change-payment-view', [
        PaymentsController::class,
        'getChangePaymentView'
    ]);

    Route::post('/cliente/{client}/pedido/{order}/pagamento/{payment}', [
        PaymentsController::class,
        'patch'
    ]);
});
