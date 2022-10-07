<?php

namespace App\GraphQL\Mutations;

use App\Models\Receipt;
use App\Util\Formatter;
use App\Util\Helper;
use App\Util\Mask;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class ReceiptCreate
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $data = $this->getFormattedData($args);
        dd('algo aqui');
        $this->validator($data)->validate();

        $data['filename'] = $this->generateReceiptPDF($data);

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

    public function formatData($data, $settings)
    {
        $data['value'] = Mask::currencyBRL($data['value']);
        $data['value'] = Str::replace('R$' . chr(194), '', $data['value']);
        $data['value'] = Str::substr($data['value'], 1);

        $settings->content = Str::replace('%cliente%', '<b>' . $data['client'] . '</b>', $settings->content);
        $settings->content = Str::replace('%valor%', '<b>' . $data['value'] . '</b>', $settings->content);
        $settings->content = Str::replace('%produto%', '<b>' . $data['product'] . '</b>', $settings->content);

        $date = Carbon::createFromFormat('Y-m-d', $data['date']);
        $date = $date->isoFormat('DD \d\e MMMM \d\e YYYY');
        $settings->date = Str::replace('%data%', $date, $settings->date);

        return [$data, $settings];
    }

    public function getFormattedData(array $data)
    {
        return (new Formatter($data))
            ->currencyBRL('value')
            ->date('date')
            ->get();
    }

    public function validator(array $data)
    {
        return Validator::make($data, [
            'client' => ['required', 'string'],
            'product' => ['required', 'string'],
            'date' => ['required', 'date'],
            'value' => ['required', 'numeric'],
            'has_signature' => ['required', 'boolean']
        ], $this->errorMessages());
    }

    public function errorMessages()
    {
        return [
            'client.required' => __('validation.rules.required'),
            'product.required' => __('validation.rules.required'),
            'date.required' => __('validation.rules.required'),
            'value.required' => __('validation.rules.required'),
        ];
    }
}
