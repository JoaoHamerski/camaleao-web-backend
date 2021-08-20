<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductionCalendarController;

Route::prefix('calendario-de-producao')
    ->middleware('role:gerencia,atendimento')
    ->group(function () {
        Route::get('/', [
            ProductionCalendarController::class,
            'index'
        ])->name('index');
    });
