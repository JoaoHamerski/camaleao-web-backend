<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ActivitiesController;

Route::middleware('role:gerencia')->group(function () {
    Route::get('/atividades', [
        ActivitiesController::class, 'index'
    ])->name('index');
});
