<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StatusController;

Route::post('/cliente/{client}/pedido/{order}/alterar-status', [
    StatusController::class, 'patch'
])->name('patch');
