<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClothingTypesController;

Route::prefix('tipos-de-roupas')->group(function () {
    Route::middleware('role:gerencia,atendimento')->group(function () {
        Route::get('/list', [
            ClothingTypesController::class,
            'list'
        ]);
    });

    Route::middleware('role:gerencia')->group(function () {
        Route::get('', [
            ClothingTypesController::class,
            'index'
        ])->name('index');

        Route::post('', [
            ClothingTypesController::class,
            'store'
        ]);

        Route::post('/{clothingType}/change-commission', [
            ClothingTypesController::class,
            'changeComission'
        ]);

        Route::patch('/{clothingType}/toggle-hide', [
            ClothingTypesController::class,
            'toggleHide'
        ]);

        Route::patch('/update-order', [
            ClothingTypesController::class,
            'updateOrder'
        ]);

        Route::patch('/{clothingType}', [
            ClothingTypesController::class,
            'update'
        ]);
    });
});
