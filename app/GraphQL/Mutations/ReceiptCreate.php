<?php

namespace App\GraphQL\Mutations;

use App\GraphQL\Traits\ReceiptTrait;
use App\Models\Receipt;
use App\Util\Mask;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;

class ReceiptCreate
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

        $data['filename'] = $this->generateReceiptPDF($data);
        $data['settings'] = json_encode(Receipt::getReceiptSettings());

        return Receipt::create($data);
    }

    public function generateReceiptPDF(array $data)
    {
        $settings = Receipt::getReceiptSettings();
        @list($data, $settings) = $this->formatData($data, $settings);

        $pdf = PDF::loadView(
            'pdf.receipts.template',
            compact('data', 'settings')
        );

        $filename = Str::random(40) . '.pdf';
        $pdf->save(storage_path("app/receipts/$filename"));

        return $filename;
    }
}
