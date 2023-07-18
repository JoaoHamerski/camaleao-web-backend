<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use Barryvdh\DomPDF\Facade as PDF;

class PDFPreviewReceipt extends PDFController
{
    public function __invoke()
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
}
