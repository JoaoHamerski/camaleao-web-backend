<?php

namespace App\GraphQL\Mutations;

use App\Models\Receipt;
use App\GraphQL\Traits\ReceiptTrait;
use Barryvdh\DomPDF\Facade as PDF;

class ReceiptUpdate
{
    use ReceiptTrait;

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $data = $this->getFormattedData($args);
        $this->validator($data)->validate();

        $receipt = Receipt::find($args['id']);

        $this->regenerateReceiptPDF($data, $receipt);

        $receipt->update($data);
        return $receipt;
    }

    public function regenerateReceiptPDF($data, Receipt $receipt): void
    {
        @list($data, $settings) = $this->formatData(
            $data,
            json_decode($receipt->settings)
        );

        $pdf = PDF::loadView(
            'pdf.receipts.template',
            compact('data', 'settings')
        );

        $pdf->save(storage_path("app/receipts/$receipt->filename"));
    }
}
