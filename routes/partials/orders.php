<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrdersController;

Route::middleware('role:gerencia,atendimento')->group(function () {
    Route::prefix('cliente/{client}')->group(function () {
        Route::get('/pedido/{order}/json', [
            OrdersController::class, 'json'
        ]);

        Route::get('/pedidos/list', [
            OrdersController::class,
            'list'
        ]);

        Route::get('/novo-pedido', [
            OrdersController::class,
            'create'
        ])->name('create');

        Route::post('/novo-pedido', [
            OrdersController::class,
            'store'
        ])->name('store');

        Route::get('/pedido/{order}/editar', [
            OrdersController::class,
            'edit'
        ])->name('edit');

        Route::patch('/pedido/{order}/editar', [
            OrdersController::class,
            'update'
        ])->name('patch');

        Route::delete('/pedido/{order}/deletar', [
            OrdersController::class,
            'destroy'
        ])->name('destroy');

        Route::post('/pedido/{order}/toggle-order', [
            OrdersController::class,
            'toggleOrder'
        ])->name('toggleOrder');

        Route::post('/pedido/{order}/editar/delete-file', [
            OrdersController::class,
            'deleteFile'
        ]);

        Route::get('/pedido/{order}/pdf-pedido', [
            OrdersController::class,
            'generateOrderPDF'
        ])->name('order-pdf');

        Route::get('/pedido/{order}', [
            OrdersController::class, 'show'
        ])->name('show');

        Route::post('/pedido/{order}/file-view', [
            OrdersController::class, 'showFile'
        ])->name('showFile');
    });

    Route::prefix('pedidos')->group(function () {
        Route::get('/', [
            OrdersController::class,
            'index'
        ])->name('index');

        Route::get('/order-commission', [
            OrdersController::class,
            'getOrderCommission'
        ]);

        Route::post('/change-order-commission', [
            OrdersController::class,
            'changeOrderCommission'
        ]);

        Route::get('/relatorio-data-producao', [
            OrdersController::class,
            'generateReportProductionDate'
        ])->name('reportProductionDate');

        Route::get('/relatorio', [
            OrdersController::class,
            'generateReport'
        ])->name('report');
    });
});
