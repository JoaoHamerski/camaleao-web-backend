<?php

use App\Http\Controllers\ImagesController;
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

Route::prefix('images')->name('images.')->group(function () {
    Route::get('/view', [
        ImagesController::class,
        'show'
    ])->name('show');
});

Route::prefix('pdf')->name('pdf.')->group(function () {
    Route::get('receipt/preview', [
        PDFsController::class,
        'previewReceipt'
    ])->name('receipt-preview');

    Route::get('receipt/{receipt}', [
        PDFsController::class,
        'showReceipt'
    ])->name('receipt');

    Route::get('orders/report', [
        PDFsController::class,
        'ordersReport'
    ])->name('orders-report');

    Route::get('orders/report/print-date', [
        PDFsController::class,
        'ordersReportPrintDate'
    ])->name('orders-report-print-date');

    Route::get('orders/report/{order}', [
        PDFsController::class,
        'orderReport'
    ])->name('order-report');

    Route::get('weekly-calendar', [
        PDFsController::class,
        'ordersWeeklyCalendar'
    ])->name('orders-weekly-calendar');

    Route::get('expenses/report', [
        PDFsController::class,
        'expensesReport'
    ])->name('expenses-report');
});
