<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotesController;
use App\Http\Controllers\CookieController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\Auth\LoginController;

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

Auth::routes();

Route::post('/set-cookie', [CookieController::class, 'setCookie']);
Route::delete('/set-cookie', [CookieController::class, 'deleteCookie']);

Route::name('auth.')->group(function() {
	Route::get('/entrar', [LoginController::class, 'showLoginForm'])->name('showLoginForm');
	Route::post('/login', [LoginController::class, 'login'])->name('login');
});

Route::middleware('auth')->group(function() {
	Route::get('/sair', [LoginController::class, 'logout'])->name('auth.logout');
	Route::name('clients.')->group(function() {
		Route::get('/', [ClientsController::class, 'index'])->name('index');
		Route::get('/cliente/{client}', [ClientsController::class, 'show'])->name('show');
		Route::post('/novo-cliente', [ClientsController::class, 'store'])->name('store');
		Route::patch('/cliente/{client}', [ClientsController::class, 'patch'])->name('patch');
		Route::delete('/cliente/{client}', [ClientsController::class, 'destroy'])->name('destroy');
	});

	Route::name('orders.')->group(function() {
		Route::get('/pedidos', [OrdersController::class, 'index'])->name('index');
		Route::post('/pedidos/relatorio', [OrdersController::class, 'generateReport'])->name('report');
		Route::post('/pedidos/relatorio-data-producao', [OrdersController::class, 'generateReportProductionDate'])->name('reportProductionDate');
		Route::get('/cliente/{client}/pedido/{order}/pdf-pedido', [OrdersController::class, 'generateOrderPDF'])->name('order-pdf');
		Route::get('/cliente/{client}/pedido/{order}/editar', [OrdersController::class, 'edit'])->name('edit');
		Route::patch('/cliente/{client}/pedido/{order}/editar', [OrdersController::class, 'patch'])->name('patch');
		Route::post('/cliente/{client}/pedido/{order}/editar/delete-file', [OrdersController::class, 'deleteFile']);
		Route::get('/cliente/{client}/novo-pedido', [OrdersController::class, 'create'])->name('create');
		Route::post('/cliente/{client}/novo-pedido', [OrdersController::class, 'store'])->name('store');
		Route::post('/cliente/{client}/pedido/{order}/file-view', [OrdersController::class, 'showFile'])->name('showFile');
		Route::get('/cliente/{client}/pedido/{order}', [OrdersController::class, 'show'])->name('show');
		Route::delete('/cliente/{client}/pedido/{order}', [OrdersController::class, 'destroy'])->name('destroy');
		Route::post('/cliente/{client}/pedido/{order}/toggle-order', [OrdersController::class, 'toggleOrder'])->name('toggleOrder');
	});

	Route::name('payments.')->group(function() {
		Route::post('/cliente/{client}/pedido/{order}/new-payment', [PaymentsController::class, 'store'])->name('store');
	});

	Route::name('notes.')->group(function() {
		Route::post('/cliente/{client}/pedido/{order}/new-note', [NotesController::class, 'store'])->name('store');
		Route::delete('/cliente/{client}/pedido/{order}/delete-note/{note}', [NotesController::class, 'destroy']);
	});

	Route::name('status.')->group(function() {
		Route::post('/cliente/{client}/pedido/{order}/alterar-status', [StatusController::class, 'patch'])->name('patch');
	});
});