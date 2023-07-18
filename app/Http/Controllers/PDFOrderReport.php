<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;

class PDFOrderReport extends PDFController
{
    public function __invoke(Request $request, Order $order)
    {
        $pdf = PDF::loadView('pdf.order.index', [
            'order' => $order,
            'title' => "Relatório de pedido - $order->code"
        ]);

        return $pdf->stream("pedido-$order->code-em-{$this->generatedAt}.pdf");
    }
}
