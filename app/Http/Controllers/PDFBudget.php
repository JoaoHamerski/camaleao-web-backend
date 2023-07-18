<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Str;

class PDFBudget extends PDFController
{
    public function __invoke(Budget $budget)
    {
        $filename = 'orcamento-' . Str::slug($budget->client) . '.pdf';
        $budget = $budget->getFormattedToPDF($budget);
        $settings = $budget->settings;

        $total = array_reduce($budget->product_items, function ($sum, $productItem) {
            return bcadd($sum, bcmul($productItem->quantity, $productItem->value));
        }, 0);

        $pdf = PDF::loadView(
            'pdf.budgets.template',
            compact(['budget', 'settings', 'total'])
        );

        return $pdf->stream($filename);
    }
}
