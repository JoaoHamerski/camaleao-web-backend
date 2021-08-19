<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ViasController;

Route::middleware('role:gerencia,atendimento')->group(function () {
    Route::get('/pagamentos/vias/list', [
        ViasController::class,
        'list'
    ]);
});
