<?php

namespace App\GraphQL\Mutations;

use App\Util\Formatter;
use App\Models\AppConfig;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommissionValueUpsert
{
    const CONFIG_KEY = 'print_commission';
    const CONFIG_NAME = 'orders';

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $data = (new Formatter($args))
            ->currencyBRL('value')
            ->get();

        Validator::make($data, [
            'value' => ['required', 'numeric']
        ])->validate();

        $config = AppConfig::set(self::CONFIG_NAME, self::CONFIG_KEY, $data['value']);

        $this->logCommissionValue($data, $config);

        return AppConfig::get(self::CONFIG_NAME, self::CONFIG_KEY);
    }

    public function logCommissionValue($data, $config)
    {
        $description = [
            'type' => 'updated',
            'placeholderText' => ':causer alterou a comissão da estampa para :attribute',
            'causerProps' => ['name' => Auth::user()->name],
            'subjectProps' => [],
            'attributesProps' => [
                'commission_value' => $data['value']
            ]
        ];

        activity('configs_orders')
            ->causedBy(Auth::user())
            ->performedOn($config)
            ->log(json_encode($description));
    }
}
