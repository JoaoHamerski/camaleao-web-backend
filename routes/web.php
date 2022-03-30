<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PDFsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('pdf')->name('pdf.')->group(function () {
    Route::get('orders/report', [
        PDFsController::class,
        'ordersReport'
    ])->name('orders-report');

    Route::get('orders/report/production-date', [
        PDFsController::class,
        'ordersReportProductionDate'
    ])->name('orders-report-production-date');

    Route::get('orders/report/{order}', [
        PDFsController::class,
        'orderReport'
    ])->name('order-report');

    Route::get('/expenses/report', [
        PDFsController::class,
        'expensesReport'
    ])->name('expenses-report');
});
