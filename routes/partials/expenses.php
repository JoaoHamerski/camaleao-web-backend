<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExpensesController;

Route::prefix('despesas')->group(function () {
    Route::middleware('role:gerencia,atendimento')->group(function () {
        Route::get('/', [
            ExpensesController::class,
            'index'
        ])->name('index');

        Route::get('/cadastro', [
            ExpensesController::class,
            'create'
        ])->name('create');

        Route::get('/cadastro/get-inline-form', [
            ExpensesController::class,
            'getInlineForm'
        ]);

        Route::post('/cadastro', [
            ExpensesController::class,
            'store'
        ])->name('store');

        Route::get('/{expense}/get-edit-form', [
            ExpensesController::class,
            'getEditForm'
        ]);

        Route::patch('/{expense}', [
            ExpensesController::class,
            'patch'
        ])->name('patch');

        Route::get('/{expense}/get-view-receipt', [
            ExpensesController::class,
            'getViewReceipt'
        ]);
    });

    Route::middleware('role:gerencia')->group(function () {
        Route::get('/relatorio', [
            ExpensesController::class,
            'report'
        ])->name('report');

        Route::delete('/{expense}/deletar', [
            ExpensesController::class, 'destroy'
        ])->name('destroy');

        Route::delete('/{expense}/delete-receipt', [
            ExpensesController::class, 'destroyReceipt'
        ]);
    });
});
