<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BackupController;

Route::middleware('role:gerencia')->group(function () {
    Route::get('/backup', [
        BackupController::class,
        'index'
    ])->name('index');

    Route::get('/backup/download', [
        BackupController::class, 'download'
    ])->name('download');
});
