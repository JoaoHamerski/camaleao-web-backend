<?php

namespace App\GraphQL\Traits;

use App\Util\Mask;
use Carbon\Carbon;
use App\Util\Formatter;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

trait ReceiptTrait
{
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
            'id' => ['sometimes', 'required', 'exists:receipts'],
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
