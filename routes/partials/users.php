<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;

Route::middleware('role:gerencia')->prefix('usuarios')->group(function () {
    Route::get('/', [
        UsersController::class,
        'index'
    ])->name('index');

    Route::post('/', [
        UsersController::class,
        'store'
    ])->name('store');

    Route::post('/{user}/change-role', [
        UsersController::class,
        'changeRole'
    ])->name('changeRole');

    Route::get('/{user}/get-change-role-form', [
        UsersController::class,
        'getChangeRoleForm'
    ]);

    Route::delete('/{user}/deletar', [
        UsersController::class,
        'destroy'
    ])->name('destroy');
});

Route::prefix('minha-conta')->group(function () {
    Route::get('/', [
        UsersController::class,
        'myAccount'
    ])->name('my-account');

    Route::patch('/', [
        UsersController::class,
        'patch'
    ])->name('patch');

    Route::delete('/deletar', [
        UsersController::class,
        'destroyOwnAccount'
    ])->name('destroyOwnAccount');
});
