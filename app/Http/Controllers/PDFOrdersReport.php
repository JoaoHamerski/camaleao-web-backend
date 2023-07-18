<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Traits\QueryOrderTrait;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Arr;

class PDFOrdersReport extends PDFController
{
    use QueryOrderTrait;

    public function __invoke(Request $request)
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

        return $pdf->stream("pedidos-geral-{$this->generatedAt}.pdf");
    }
}
