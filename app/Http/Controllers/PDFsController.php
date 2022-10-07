<?php

namespace App\Http\Controllers;

use App\Models\AppConfig;
use App\Util\Mask;
use Carbon\Carbon;
use App\Util\Helper;
use App\Models\Order;
use App\Models\Status;
use App\Models\Expense;
use App\Models\Receipt;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PDFsController extends Controller
{
    protected $generatedAt = '';
    protected static $FIELD_TYPES = [
        'seam_date' => 'Costuras',
        'print_date' => 'Estampas',
        'delivery_date' => 'Entregas',
    ];

    public function __construct(Request $request)
    {
        if (!$request->hasValidSignature()) {
            // abort(401);
        }
    }

    public function previewReceipt()
    {
        $settings = Receipt::getReceiptSettings();

        return PDF::loadView(
            'pdf.receipts.template',
            [
                'settings' => $settings,
                'preview' => true
            ]
        )->stream('pre-visualizacao.pdf');
    }

    public function showReceipt(Receipt $receipt)
    {
        $clientName = Str::slug(explode(' ', $receipt->client)[0]);
        $date = Carbon::createFromFormat('Y-m-d', $receipt->date);
        $date = $date->format('d-m-Y');
        $filename = "recibo-$clientName-$date.pdf";

        return response()->file($receipt->filepath, [
            'Content-disposition' => 'inline; filename="' . $filename . '"'
        ]);
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

    public function ordersReportPrintDate(Request $request)
    {
        $orders = Order::query();
        $date = Carbon::createFromFormat('Y-m-d', $request->date);

        $request->merge(['print_date' => $date->toDateString()]);
        $request->query->remove('date');

        $orders = $this->queryOrders($orders, $request->all(), $request);
        $quantity = $orders->sum('quantity');

        $pdf = PDF::loadView('pdf.orders-print-date.index', [
            'orders' => $orders->get(),
            'title' => 'Relatório de estampa',
            'subtitle' => Str::upper(Helper::plural($quantity, 'F', 'peça'))
        ]);

        return $pdf->stream('relatorio-estampa-' . $date->format('d-m-Y') . '.pdf');
    }

    private function getTitleForOrdersWeeklyCalendar(Carbon $date, $field)
    {
        $date = $date->isoFormat('DD [de] MMMM');
        $type = static::$FIELD_TYPES[$field];

        return  "$type - $date";
    }

    private function getSubtitleForOrdersWeeklyCalendar($orders, $request)
    {
        $status = null;
        $shirtPiecesText = Str::upper(
            Helper::plural($orders->sum('quantity'), 'f', 'peça')
        );

        if ($request->filled('status_id')) {
            $status = Status::find($request->status_id);

            return "$status->text - $shirtPiecesText";
        }

        return $shirtPiecesText;
    }

    private function getFilenameForOrdersWeeklyCalendar(Carbon $date, $field)
    {
        $type = Str::lower(static::$FIELD_TYPES[$field]);
        $date = $date->format('d-m-Y');

        return "calendario-semanal-$type-$date.pdf";
    }

    public function ordersWeeklyCalendar(Request $request)
    {
        Validator::make($request->all(), [
            'field' => ['required', Rule::in([
                'seam_date',
                'print_date',
                'delivery_date'
            ])],
            'status_id' => ['nullable', 'exists:status,id'],
            'date' => ['required', 'date']
        ])->validate();

        $request->merge([$request->field => $request->date]);
        $date = Carbon::createFromFormat('Y-m-d', $request->date);

        $orders = Order::query();
        $orders = $this->queryOrders($orders, $request->all(), $request);

        if ($request->field === 'print_date') {
            $orders->orderBy('order', 'ASC');
            $orders->orderBy('created_at', 'DESC');
        }

        $pdf = PDF::loadView('pdf.weekly-calendar.index', [
            'title' =>  $this->getTitleForOrdersWeeklyCalendar($date, $request->field),
            'subtitle' => $this->getSubtitleForOrdersWeeklyCalendar($orders, $request),
            'orders' => $orders->get(),
        ]);

        return $pdf->stream(
            $this->getFilenameForOrdersWeeklyCalendar(
                $date,
                $request->field
            )
        );
    }

    public function queryOrders($orders, $data, Request $request)
    {
        if ($request->filled('city_id')) {
            $orders->whereHas('client.city', function ($query) use ($data) {
                $query->where('id', $data['city_id']);
            });
        }

        if ($request->filled('status_id')) {
            if (is_array($data['status_id'])) {
                $orders->whereIn('status_id', $data['status_id']);
            } else {
                $orders->where('status_id', $data['status_id']);
            }
        }

        if ($request->filled('closed_at')) {
            $orders->whereDate('closed_at', $data['closed_at']);
        }

        if ($request->filled('delivery_date')) {
            $orders->whereDate('delivery_date', $data['delivery_date']);
        }

        if ($request->filled('print_date')) {
            $orders->whereDate('print_date', $data['print_date']);
        }

        if ($request->filled('seam_date')) {
            $orders->whereDate('seam_date', $data['seam_date']);
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
