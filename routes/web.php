<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ViasController;
use App\Http\Controllers\NotesController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\CitiesController;
use App\Http\Controllers\CookieController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\BranchesController;
use App\Http\Controllers\CashFlowController;
use App\Http\Controllers\ExpensesController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\ActivitiesController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ExpenseTypesController;
use App\Http\Controllers\ClothingTypesController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\ShippingCompaniesController;
use App\Models\ClothingType;

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

Route::post('/set-cookie', [CookieController::class, 'setCookie']);
Route::delete('/set-cookie', [CookieController::class, 'deleteCookie']);

Route::name('auth.')->group(function () {
    Route::get('/entrar', [LoginController::class, 'showLoginForm'])->name('showLoginForm');
    Route::post('/login', [LoginController::class, 'login'])->name('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/sair', [LoginController::class, 'logout'])->name('auth.logout');

    Route::name('production.')->group(function () {
        Route::middleware('role:gerencia')->group(function () {
            Route::get('/lista-de-producao', [ProductionController::class, 'indexAdmin'])->name('indexAdmin');
        });

        Route::middleware('role:costura,estampa')->group(function () {
            Route::get('/producao', [ProductionController::class, 'index'])->name('home');
            Route::get('/producao/get-commissions', [ProductionController::class, 'getCommissions']);
            Route::post('/producao/{commissionUser}/confirm', [ProductionController::class, 'assignConfirmation']);
        });
    });

    Route::name('users.')->group(function () {
        Route::middleware('role:gerencia')->group(function () {
            Route::get('/usuarios', [UsersController::class, 'index'])->name('index');
            Route::post('/usuarios', [UsersController::class, 'store'])->name('store');
            Route::post('/usuarios/{user}/change-role', [UsersController::class, 'changeRole'])->name('changeRole');
            Route::get('/usuarios/{user}/get-change-role-form', [UsersController::class, 'getChangeRoleForm']);
            Route::delete('/usuarios/{user}/deletar', [UsersController::class, 'destroy'])->name('destroy');
        });

        Route::delete('/minha-conta/deletar', [UsersController::class, 'destroyOwnAccount'])->name('destroyOwnAccount');
        Route::get('/minha-conta', [UsersController::class, 'myAccount'])->name('my-account');
        Route::patch('/minha-conta', [UsersController::class, 'patch'])->name('patch');
    });

    Route::name('clients.')->group(function () {
        Route::middleware('role:gerencia,atendimento,design')->group(function () {
            Route::get('/', [ClientsController::class, 'index'])->name('index');
            Route::get('/clientes/list', [ClientsController::class, 'list']);
            Route::get('/clientes/{client}', [ClientsController::class, 'show'])->name('show');
            Route::get('/clientes/{client}/json', [ClientsController::class, 'client']);
        });

        Route::middleware('role:gerencia,atendimento')->group(function () {
            Route::post('/clientes', [ClientsController::class, 'store'])->name('store');
            Route::patch('/clientes/{client}', [ClientsController::class, 'update'])->name('update');
        });
        
        Route::middleware('role:gerencia')->group(function () {
            Route::delete('/clientes/{client}', [ClientsController::class, 'destroy'])->name('destroy');
        });
    });

    Route::name('orders.')->group(function () {
        Route::get('/cliente/{client}/pedido/{order}/pdf-pedido', [OrdersController::class, 'generateOrderPDF'])->name('order-pdf');
        Route::get('/cliente/{client}/pedido/{order}', [OrdersController::class, 'show'])->name('show');
        Route::post('/cliente/{client}/pedido/{order}/file-view', [OrdersController::class, 'showFile'])->name('showFile');

        Route::middleware('role:gerencia,atendimento')->group(function () {
            Route::get('/cliente/{client}/pedido/{order}/json', [OrdersController::class, 'json']);
            Route::get('/cliente/{client}/pedidos/list', [OrdersController::class, 'list']);
            Route::get('/pedidos', [OrdersController::class, 'index'])->name('index');
            Route::get('/pedidos/order-commission', [OrdersController::class, 'getOrderCommission']);
            Route::post('/pedidos/change-order-commission', [OrdersController::class, 'changeOrderCommission']);
            Route::get('/cliente/{client}/novo-pedido', [OrdersController::class, 'create'])->name('create');
            Route::post('/cliente/{client}/novo-pedido', [OrdersController::class, 'store'])->name('store');
            Route::get('/cliente/{client}/pedido/{order}/editar', [OrdersController::class, 'edit'])->name('edit');
            Route::patch('/cliente/{client}/pedido/{order}/editar', [OrdersController::class, 'update'])->name('patch');
            Route::delete('/cliente/{client}/pedido/{order}/deletar', [OrdersController::class, 'destroy'])->name('destroy');
            Route::get('/pedidos/relatorio-data-producao', [OrdersController::class, 'generateReportProductionDate'])->name('reportProductionDate');
            Route::get('/pedidos/relatorio', [OrdersController::class, 'generateReport'])->name('report');
            Route::post('/cliente/{client}/pedido/{order}/toggle-order', [OrdersController::class, 'toggleOrder'])->name('toggleOrder');
            Route::post('/cliente/{client}/pedido/{order}/editar/delete-file', [OrdersController::class, 'deleteFile']);
        });
    });

    Route::name('expenses.')->middleware('role:gerencia,atendimento')->group(function () {
        Route::get('/despesas', [ExpensesController::class, 'index'])->name('index');
        Route::get('/despesas/cadastro', [ExpensesController::class, 'create'])->name('create');
        Route::get('/despesas/cadastro/get-inline-form', [ExpensesController::class, 'getInlineForm']);
        Route::post('/despesas/cadastro', [ExpensesController::class, 'store'])->name('store');
        Route::get('/despesas/{expense}/get-edit-form', [ExpensesController::class, 'getEditForm']);
        Route::patch('/despesas/{expense}', [ExpensesController::class, 'patch'])->name('patch');
        Route::get('/despesas/{expense}/get-view-receipt', [ExpensesController::class, 'getViewReceipt']);

        Route::middleware('role:gerencia')->group(function () {
            Route::get('/despesas/relatorio', [ExpensesController::class, 'report'])->name('report');
            Route::delete('/despesas/{expense}/deletar', [ExpensesController::class, 'destroy'])->name('destroy');
            Route::delete('/despesas/{expense}/delete-receipt', [ExpensesController::class, 'destroyReceipt']);
        });
    });

    Route::name('cash-flow.')->middleware('role:gerencia')->group(function () {
        Route::get('/fluxo-de-caixa', [CashFlowController::class, 'index'])->name('index');
        Route::get('/fluxo-de-caixa/get-details', [CashFlowController::class, 'getDetails']);
    });

    Route::name('expense_types.')->middleware('role:gerencia')->group(function () {
        Route::post('/despesas/tipo-de-despesa', [ExpenseTypesController::class, 'store'])->name('store');
        Route::patch('/despesas/tipo-de-despesa/{expense_type}', [ExpenseTypesController::class, 'patch'])->name('patch');
    });

    Route::name('via')->middleware('role:gerencia,atendimento')->group(function () {
        Route::get('/pagamentos/vias/list', [ViasController::class, 'list']);
    });

    Route::name('payments.')->middleware('role:gerencia,atendimento')->group(function () {
        Route::post('/cliente/{client}/pedido/{order}/new-payment', [PaymentsController::class, 'store'])->name('store');
        Route::get('/cliente/{client}/pedido/{order}/pagamento/{payment}/get-change-payment-view', [PaymentsController::class, 'getChangePaymentView']);
        Route::post('/cliente/{client}/pedido/{order}/pagamento/{payment}', [PaymentsController::class, 'patch']);
    });

    Route::name('notes.')->group(function () {
        Route::post('/cliente/{client}/pedido/{order}/new-note', [NotesController::class, 'store'])->name('store');
        Route::delete('/cliente/{client}/pedido/{order}/delete-note/{note}', [NotesController::class, 'destroy']);
    });

    Route::name('status.')->group(function () {
        Route::post('/cliente/{client}/pedido/{order}/alterar-status', [StatusController::class, 'patch'])->name('patch');
    });

    Route::name('financial.')->middleware('role:gerencia')->group(function () {
        Route::get('/financeiro', [FinancialController::class, 'index'])->name('index');
    });

    Route::name('activities.')->middleware('role:gerencia')->group(function () {
        Route::get('/atividades', [ActivitiesController::class, 'index'])->name('index');
    });

    Route::middleware('role:gerencia')->group(function () {
        Route::get('/download-backup', [BackupController::class, 'download'])->name('backups.download');
    });

    Route::name('cities.')->group(function () {
        Route::middleware('role:atendimento,gerencia')->group(function () {
            Route::get('/gerenciamento/cidades/list', [CitiesController::class, 'list']);
            Route::post('/gerenciamento/cidades', [CitiesController::class, 'store']);
            Route::get('/gerenciamento/cidades/estados/list', [CitiesController::class, 'states']);
        });
        
        Route::middleware('role:gerencia')->group(function () {
            Route::get('/gerenciamento/cidades', [CitiesController::class, 'index'])->name('index');
            Route::get('/gerenciamento/cidades/{city}', [CitiesController::class, 'show']);
            Route::patch('/gerenciamento/cidades/{city}', [CitiesController::class, 'patch']);
            Route::patch('/gerenciamento/cidades', [CitiesController::class, 'patchMany']);
            Route::post('/gerenciamento/cidades/{city}/replace', [CitiesController::class, 'replace']);
        });
    });

    Route::name('branches.')->group(function () {
        Route::get('/gerenciamento/filiais/list', [BranchesController::class, 'list']);
        Route::middleware('role:gerencia')->group(function () {
            Route::get('/gerenciamento/filiais', [BranchesController::class, 'index'])->name('index');
            Route::post('/gerenciamento/filiais', [BranchesController::class, 'store']);
            Route::patch('/gerenciamento/filiais/{branch}', [BranchesController::class, 'update']);
            Route::delete('/gerenciamento/filiais/{branch}', [BranchesController::class, 'destroy']);
        });
    });

    Route::name('shipping-companies.')->group(function () {
        Route::middleware('role:gerencia,atendimento')->group(function () {
            Route::get('/transportadoras/list', [ShippingCompaniesController::class, 'list']);
        });
        
        Route::middleware('role:gerencia')->group(function () {
            Route::patch('/transportadoras/{shippingCompany}', [ShippingCompaniesController::class, 'update']);
            Route::post('/transportadoras', [ShippingCompaniesController::class, 'store']);
            Route::delete('/transportadoras/{shippingCompany}', [ShippingCompaniesController::class, 'destroy']);
        });
    });

    Route::name('backup.')->middleware('role:gerencia')->group(function () {
        Route::get('/backup', [BackupController::class, 'index'])->name('index');
        Route::get('/backup/download', [BackupController::class, 'download'])->name('download');
    });

    Route::name('clothing-types.')->group(function () {
        Route::middleware('role:gerencia,atendimento')->group(function () {
            Route::get('/tipos-de-roupas/list', [ClothingTypesController::class, 'list']);
        });

        Route::middleware('role:gerencia')->group(function () {
            Route::get('/tipos-de-roupas', [ClothingTypesController::class, 'index'])->name('index');
            Route::post('/tipos-de-roupas', [ClothingTypesController::class, 'store']);
            Route::post('/tipos-de-roupas/{clothingType}/change-commission', [ClothingTypesController::class, 'changeComission']);
            Route::patch('/tipos-de-roupas/{clothingType}/toggle-hide', [ClothingTypesController::class, 'toggleHide']);
            Route::patch('/tipos-de-roupas/update-order', [ClothingTypesController::class, 'updateOrder']);
            Route::patch('/tipos-de-roupas/{clothingType}', [ClothingTypesController::class, 'update']);
        });
    });

    Route::name('payments.')->group(function () {
        Route::middleware('role:gerencia,atendimento')->group(function () {
            Route::get('/caixa-diario', [
                PaymentsController::class,
                'index'
            ])->name('daily');
            
            Route::get('/caixa-diario/payments', [
                PaymentsController::class,
                'getPaymentsOfDay'
            ]);

            Route::get('/caixa-diario/get-total-pendencies', [
                PaymentsController::class,
                'getTotalPendencies'
            ]);

            Route::get('/caixa-diario/get-pendencies', [
                PaymentsController::class,
                'getPendencies'
            ]);

            Route::post('/caixa-diario/{payment}/assign-confirmation', [
                PaymentsController::class,
                'assignConfirmation'
            ]);

            Route::post('/caixa-diario/clientes/daily-payment', [
                PaymentsController::class,
                'dailyPayment'
            ]);
        });
    });
});
