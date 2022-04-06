<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Expense;
use App\Util\Mask;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;

class PDFsController extends Controller
{
    protected $generatedAt = '';

    public function __construct(Request $request)
    {
        if (!$request->hasValidSignature()) {
            abort(401);
        }

        $this->generatedAt = now()->format('d-m-Y-H-i-s');
    }

    public function expensesReport(Request $request)
    {
        $start_date = $request->start_date;
        $final_date = $request->final_date ?? $start_date;
        $expenses = Expense::whereBetween('date', [$start_date, $final_date])
            ->join('expense_types', 'expenses.expense_type_id', '=', 'expense_types.id');

        $expensesByType = $expenses
            ->clone()
            ->selectRaw('SUM(value) AS total, expense_type_id, COUNT(*) AS quantity')
            ->groupBy('expense_type_id')
            ->orderBy('expense_types.name')
            ->get();

        $expenses = $expenses
            ->orderBy('date', 'ASC')
            ->orderBy('expense_types.name', 'ASC')
            ->get();

        $dates = $this->getBetweenDates($request->start_date, $request->final_date);

        $pdf = PDF::loadView('pdf.expenses.index', [
            'title' => 'Relatório de despesas',
            'subtitle' => $dates,
            'start_date' => $request->start_date,
            'final_date' => $request->final_date,
            'expensesByType' => $expensesByType,
            'expenses' => $expenses
        ]);

        $dates = str_replace('/', '-', $dates);

        return $pdf->stream("Relatóro de despesas - $dates.pdf");
    }

    public function getBetweenDates($start_date, $final_date)
    {
        if (!$final_date) {
            return Mask::date($start_date);
        }

        return Mask::date($start_date) . ' - ' . Mask::date($final_date);
    }

    public function orderReport(Request $request, Order $order)
    {
        $pdf = PDF::loadView('pdf.order.index', [
            'order' => $order,
            'title' => "Relatório de pedido - $order->code"
        ]);

        return $pdf->stream("pedido-$order->code-em-$this->generatedAt.pdf");
    }

    public function ordersReport(Request $request)
    {
        $STATIC_TABLE_ROW_NUMBERS = 3;

        $orders = Order::notPreRegistered();
        $data = $request->all();

        $orders = $this->queryOrders($orders, $data, $request);

        $pdf = PDF::loadView('pdf.orders.index', [
            'filters' => $request->all(),
            'orders' => $orders->get(),
            'title' => 'Relatório geral de pedidos',
            'rowSpanCalc' => function ($order) use ($STATIC_TABLE_ROW_NUMBERS) {
                return count(Arr::where(
                    [$order->closed_at, $order->delivery_date],
                    fn ($item) => $item !== null
                )) + $STATIC_TABLE_ROW_NUMBERS;
            }
        ]);

        return $pdf->stream("pedidos-geral-$this->generatedAt.pdf");
    }

    public function ordersReportProductionDate(Request $request)
    {
        $orders = Order::query();
        $date = Carbon::createFromFormat('Y-m-d', $request->date);

        $request->merge(['production_date' => $date->toDateString()]);
        $request->query->remove('date');

        $orders = $this->queryOrders($orders, $request->all(), $request);
        $quantity = $orders->sum('quantity');

        $pdf = PDF::loadView('pdf.orders-production-date.index', [
            'orders' => $orders->get(),
            'title' => "Produção do dia - " . $date->format('d/m/Y'),
            'subtitle' => $quantity
                ? "$quantity PEÇAS PRODUZIDAS"
                : ''
        ]);

        return $pdf->stream("relatorio-de-producao-" . $date->format('d-m-Y') . '.pdf');
    }

    public function ordersWeeklyProduction(Request $request)
    {
        $date = $request->date;
        $formattedDate = Carbon::createFromFormat('Y-m-d', $date)
            ->isoFormat('DD [de] MMMM');

        $orders = Order::where('production_date', $date);
        $ordersClothingQuantity = $orders->count('quantity');

        $pdf = PDF::loadView('pdf.weekly-production.index', [
            'title' => "Produção de $formattedDate",
            'subtitle' => $ordersClothingQuantity
                ? "$ordersClothingQuantity PEÇAS"
                : "NENHUMA PEÇA",
            'orders' => $orders->get(),
        ]);

        return $pdf->download("weekly-production-$date.pdf");
        // return $pdf->stream("weekly-production-$date.pdf");
    }

    public function queryOrders($orders, $data, Request $request)
    {
        if ($request->filled('city_id')) {
            $orders->whereHas('client.city', function ($query) use ($data) {
                $query->where('id', $data['city_id']);
            });
        }

        if ($request->filled('status_id')) {
            $orders->where('status_id', $data['status_id']);
        }

        if ($request->filled('closed_at')) {
            $orders->whereDate('closed_at', $data['closed_at']);
        }

        if ($request->filled('delivery_date')) {
            $orders->whereDate('delivery_date', $data['delivery_date']);
        }

        if ($request->filled('production_date')) {
            $orders->whereDate('production_date', $data['production_date']);
        }

        if ($request->filled('order')) {
            $orders = $this->queryOrdersOrder($orders, $data['order']);
        }

        if ($request->filled('state')) {
            $orders = $this->queryOrdersState($orders, $data['state']);
        }

        return $orders;
    }

    public function queryOrdersOrder($query, $order)
    {
        if ($order === 'older') {
            $query->orderBy('created_at', 'ASC');
        }

        if ($order === 'newer') {
            $query->orderBy('created_at', 'DESC');
        }

        if ($order === 'delivery_date') {
            $query->orderBy('delivery_date', 'DESC');
        }

        return $query;
    }

    public function queryOrdersState($query, $state)
    {
        if ($state === 'open') {
            $query->whereNull('closed_at');
        }

        if ($state === 'all') {
            //
        }

        return $query;
    }
}
