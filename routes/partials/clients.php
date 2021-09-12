<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientsController;

// Route::middleware('role:gerencia,atendimento')->group(function () {
//     Route::get('/', [
//         ClientsController::class,
//         'index'
//     ])->name('index');
// });

Route::prefix('clientes')->group(function () {
    Route::middleware('role:gerencia,atendimento')->group(function () {
        Route::get('/list', [
            ClientsController::class,
            'list'
        ])->name('algo');

        Route::get('/{client}', [
            ClientsController::class,
            'show'
        ])->name('show');

        Route::get('/{client}/json', [
            ClientsController::class,
            'client'
        ]);
    });

    Route::middleware('role:gerencia,atendimento')->group(function () {
        Route::post('', [
            ClientsController::class,
            'store'
        ])->name('store');

        Route::patch('/{client}', [
            ClientsController::class,
            'update'
        ])->name('update');
    });

    Route::middleware('role:gerencia')->group(function () {
        Route::delete('/{client}', [
            ClientsController::class,
            'destroy'
        ])->name('destroy');
    });
});
