<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShippingCompaniesController;

Route::prefix('transportadoras')->group(function () {
    Route::middleware('role:gerencia,atendimento')->group(function () {
        Route::get('/list', [
            ShippingCompaniesController::class,
            'list'
        ]);
    });

    Route::middleware('role:gerencia')->group(function () {
        Route::patch('/{shippingCompany}', [
            ShippingCompaniesController::class,
            'update'
        ]);

        Route::post('/', [
            ShippingCompaniesController::class,
            'store'
        ]);

        Route::delete('/{shippingCompany}', [
            ShippingCompaniesController::class,
            'destroy'
        ]);
    });
});
