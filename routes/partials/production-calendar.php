<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductionCalendarController;

Route::prefix('calendario-de-producao')
    ->group(function () {
        Route::middleware('role:gerencia,atendimento,costura,estampa')->group(function () {
            Route::get('/', [
                ProductionCalendarController::class,
                'index'
            ])->name('index');

            Route::get('/pedidos/semana', [
                ProductionCalendarController::class,
                'ordersByWeek'
            ]);
        });

        Route::middleware('role:gerencia,atendimento')->group(function () {
            Route::post('/pedidos/novo', [
                ProductionCalendarController::class,
                'storeOrder'
            ]);

            Route::get('/pedidos/pendentes', [
                ProductionCalendarController::class,
                ''
            ]);
        });
    });
