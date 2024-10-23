<?php

use App\Http\Controllers\ImagesController;
use App\Http\Controllers\PDFBudget;
use App\Http\Controllers\PDFExpenseReport;
use App\Http\Controllers\PDFOrderReport;
use App\Http\Controllers\PDFOrdersReport;
use App\Http\Controllers\PDFOrdersSizesReport;
use App\Http\Controllers\PDFPreviewReceipt;
use App\Http\Controllers\PDFReceipt;
use App\Http\Controllers\PDFWeeklyCalendar;
use Illuminate\Support\Facades\Route;

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
    Route::get('receipt/preview', PDFPreviewReceipt::class)->name('receipt-preview');
    Route::get('receipt/{receipt}', PDFReceipt::class)->name('receipt');
    Route::get('budget/{budget}', PDFBudget::class)->name('budget');
    Route::get('orders/report', PDFOrdersReport::class)->name('orders-report');
    Route::get('orders/report/{order}', PDFOrderReport::class)->name('order-report');
    Route::get('weekly-calendar', PDFWeeklyCalendar::class)->name('orders-weekly-calendar');
    Route::get('expenses/report', PDFExpenseReport::class)->name('expenses-report');
    Route::get('orders/sizes', PDFOrdersSizesReport::class)->name('orders-sizes');
});
