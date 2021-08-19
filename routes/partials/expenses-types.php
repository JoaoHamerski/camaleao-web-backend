<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExpenseTypesController;

Route::prefix('despesas')->middleware('role:gerencia')->group(function () {
    Route::post('tipo-de-despesa', [
        ExpenseTypesController::class,
        'store'
    ])->name('store');

    Route::patch('tipo-de-despesa/{expense_type}', [
        ExpenseTypesController::class,
        'patch'
    ])->name('patch');
});
