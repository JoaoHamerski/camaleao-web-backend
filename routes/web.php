<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BranchesController;
use App\Http\Controllers\CashFlowController;
use App\Http\Controllers\CitiesController;
use App\Http\Controllers\ClothingTypesController;
use App\Http\Controllers\ExpensesController;
use App\Http\Controllers\ExpenseTypesController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\ShippingCompaniesController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\ViasController;

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

Route::get('/', function () {
    return response('', 200);
});

Route::get('/test', [TestController::class, 'test']);

// Route for testing
Route::get('/resource', function () {
    return new \App\Http\Resources\OrderResource(App\Models\Order::first());
});

Route::post('/login', [LoginController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

Route::middleware('auth:sanctum')->prefix('api')->group(function () {
    Route::get('/users/auth', AuthController::class);
});

Route::post('/api/sanctum/token', TokenController::class);

Route::prefix('api/clients')->name('clients.')->group(function () {
    Route::get('/', [
        ClientsController::class,
        'index'
    ])->name('index');

    Route::get('/{client}', [
        ClientsController::class,
        'show'
    ])->name('show');

    Route::get('/{client}/orders', [
        ClientsController::class,
        'orders'
    ])->name('orders');

    Route::post('/', [
        ClientsController::class,
        'store'
    ])->name('store');
});

Route::name('orders.')->group(function () {
    Route::prefix('api/orders')->group(function () {
        Route::get('/', [
            OrdersController::class,
            'index'
        ])->name('index');

        Route::get('/reports/general', [
            OrdersController::class,
            'generateGeneralOrderReport'
        ])->name('general-report');

        Route::get('/reports/production-date', [
            OrdersController::class,
            'generateReportProductionDate'
        ])->name('production-date-report');
    });

    Route::prefix('api/clients')->group(function () {
        Route::get('/{client}/orders/{order}', [
            OrdersController::class,
            'show'
        ])->name('show');

        Route::post('/{client}/new-order', [
            OrdersController::class,
            'store'
        ])->name('store');

        Route::patch('/{client}/orders/{order}', [
            OrdersController::class,
            'update'
        ])->name('update');

        Route::delete('/{client}/orders/{order}', [
            OrdersController::class,
            'destroy'
        ])->name('destroy');

        Route::get('/{client}/orders/{order}/generate-report', [
            OrdersController::class,
            'generateOrderReport'
        ])->name('generate-order-report');

        Route::post('/{client}/orders/{order}/toggle-order', [
            OrdersController::class,
            'toggleOrder'
        ])->name('toggle');

        Route::post('/{client}/orders/{order}/payments', [
            PaymentsController::class,
            'store'
        ])->name('store-payment');

        Route::patch('/{client}/orders/{order}/payments/{payment}', [
            PaymentsController::class,
            'update'
        ])->name('update-payment');

        Route::patch('/{client}/orders/{order}/update-status', [
            StatusController::class,
            'patch'
        ])->name('update-status');
    });
});

Route::prefix('api/clothing-types')->name('clothing-types.')->group(function () {
    Route::get('/', [
        ClothingTypesController::class,
        'index'
    ])->name('index');
});

Route::prefix('api/payments')->name('payments.')->group(function () {
    Route::post('/{payment}/confirm', [
        PaymentsController::class,
        'confirmPayment'
    ])->name('confirm');
});

Route::prefix('api/daily-cash')->name('daily-cash.')->group(function () {
    Route::post('/create', [
        PaymentsController::class,
        'dailyCashStore'
    ])->name('store');

    Route::get('/payments-of-day', [
        PaymentsController::class,
        'paymentsOfDay'
    ])->name('payments-of-day');

    Route::get('/pendencies', [
        PaymentsController::class,
        'pendencies'
    ])->name('pendencies');
});

Route::prefix('api/cash-flow')->name('cash-flow.')->group(function () {
    Route::get('/', [
        CashFlowController::class,
        'index'
    ])->name('index');
});

Route::prefix('api/expenses')->name('expenses.')->group(function () {
    Route::get('/', [
        ExpensesController::class,
        'index'
    ])->name('index');

    Route::get('/report', [
        ExpensesController::class,
        'report'
    ])->name('report');

    Route::post('/report-validate', [
        ExpensesController::class,
        'validateReport'
    ])->name('validate-report');

    Route::post('/', [
        ExpensesController::class,
        'store'
    ])->name('store');

    Route::patch('/{expense}', [
        ExpensesController::class,
        'update'
    ])->name('update');

    Route::delete('/{expense}', [
        ExpensesController::class,
        'destroy'
    ])->name('destroy');
});

Route::prefix('api/expense-types')->name('expense-types.')->group(function () {
    Route::get('/', [
        ExpenseTypesController::class,
        'index'
    ])->name('index');

    Route::post('/', [
        ExpenseTypesController::class,
        'store'
    ])->name('store');

    Route::patch('/{expenseType}/edit', [
        ExpenseTypesController::class,
        'patch'
    ])->name('update');
});

Route::prefix('api/status')->name('status.')->group(function () {
    Route::get('/', [
        StatusController::class,
        'index'
    ])->name('index');
});

Route::prefix('api/cities')->name('cities.')->group(function () {
    Route::get('/', [CitiesController::class, 'index'])->name('index');
});

Route::prefix('api/branches')->name('branches')->group(function () {
    Route::get('/', [BranchesController::class, 'index'])->name('index');
});

Route::prefix('api/shipping-companies')->name('shipping-companies')->group(function () {
    Route::get('/', [ShippingCompaniesController::class, 'index'])->name('index');
});

Route::prefix('api/vias')->name('vias.')->group(function () {
    Route::get('/', [ViasController::class, 'index'])->name('index');
});
