<?php

namespace App\GraphQL\Mutations;

use App\Models\Config;
use App\Util\Formatter;
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

        $commission = Config::get(self::CONFIG_NAME);

        if ($commission === null) {
            Config::new(self::CONFIG_NAME, self::CONFIG_KEY, $data['value']);

            return Config::get(self::CONFIG_NAME, self::CONFIG_KEY);
        }

        Config::set(self::CONFIG_NAME, self::CONFIG_KEY, $data['value']);

        return Config::get(self::CONFIG_NAME, self::CONFIG_KEY);
    }
}
