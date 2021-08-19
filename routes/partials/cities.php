<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CitiesController;

Route::prefix('gerenciamento/cidades')->group(function () {
    Route::middleware('role:atendimento,gerencia')->group(function () {
        Route::post('/', [
            CitiesController::class,
            'store'
        ]);

        Route::get('/list', [
            CitiesController::class,
            'list'
        ]);

        Route::get('/estados/list', [
            CitiesController::class,
            'states'
        ]);
    });

    Route::middleware('role:gerencia')->group(function () {
        Route::get('/', [
            CitiesController::class,
            'index'
        ])->name('index');

        Route::get('/{city}', [
            CitiesController::class,
            'show'
        ]);

        Route::patch('/{city}', [
            CitiesController::class,
            'patch'
        ]);

        Route::patch('/', [
            CitiesController::class,
            'patchMany'
        ]);

        Route::post('/{city}/replace', [
            CitiesController::class,
            'replace'
        ]);
    });
});
