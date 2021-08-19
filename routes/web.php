<?php

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

Route::name('cookies.')->group(__DIR__ . '/partials/cookies.php');
Route::name('auth.')->group(__DIR__ . '/partials/_auth.php');

Route::middleware('auth')->group(function () {
    Route::name('production.')->group(__DIR__ . '/partials/production.php');
    Route::name('users.')->group(__DIR__ . '/partials/users.php');
    Route::name('clients.')->group(__DIR__ . '/partials/clients.php');
    Route::name('orders.')->group(__DIR__ . '/partials/orders.php');
    Route::name('expenses.')->group(__DIR__ . '/partials/expenses.php');
    Route::name('cash-flow.')->group(__DIR__ . '/partials/cash-flow.php');
    Route::name('via.')->group(__DIR__ . '/partials/via.php');
    Route::name('expense-types.')->group(__DIR__ . '/partials/expenses-types.php');
    Route::name('payments.')->group(__DIR__ . '/partials/payments.php');
    Route::name('daily-cash.')->group(__DIR__ . '/partials/daily-cash.php');
    Route::name('order-notes.')->group(__DIR__ . '/partials/order-notes.php');
    Route::name('cities.')->group(__DIR__ . '/partials/cities.php');
    Route::name('branches.')->group(__DIR__ . '/partials/branches.php');
    Route::name('shipping-companies.')->group(__DIR__ . '/partials/shipping-companies.php');
    Route::name('clothing-types.')->group(__DIR__ . '/partials/clothing-types.php');
    Route::name('backup.')->group(__DIR__ . '/partials/backup.php');
    Route::name('status.')->group(__DIR__ . '/partials/status.php');
    Route::name('financial.')->group(__DIR__ . '/partials/financial.php');
    Route::name('activities.')->group(__DIR__ . '/partials/activities.php');
});
