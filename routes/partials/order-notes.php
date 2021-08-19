<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotesController;

Route::prefix('order-notes')->group(function () {
    Route::post('/cliente/{client}/pedido/{order}/new-note', [
        NotesController::class, 'store'
    ])->name('store');

    Route::delete('/cliente/{client}/pedido/{order}/delete-note/{note}', [
        NotesController::class,
        'destroy'
    ]);
});
