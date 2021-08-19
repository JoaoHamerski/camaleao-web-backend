<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BranchesController;

Route::prefix('gerenciamento/filiais')->group(function () {
    Route::get('/list', [
        BranchesController::class,
        'list'
    ]);

    Route::middleware('role:gerencia')->group(function () {
        Route::get('/', [
            BranchesController::class,
            'index'
        ])->name('index');

        Route::post('/', [
            BranchesController::class,
            'store'
        ]);

        Route::patch('/{branch}', [
            BranchesController::class,
            'update'
        ]);

        Route::delete('/{branch}', [
            BranchesController::class,
            'destroy'
        ]);
    });
});
