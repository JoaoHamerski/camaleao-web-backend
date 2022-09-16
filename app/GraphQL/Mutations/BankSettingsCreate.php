<?php

namespace App\GraphQL\Mutations;

use App\Models\BankSetting;
use Illuminate\Support\Facades\Validator;

class BankSettingsCreate
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'name' => ['required'],
            'date_format' => ['required'],
            'fields.*' => ['required', 'string'],
            'bank_fields' => ['required', 'array'],
            'bank_fields.*' => ['required', 'string']
        ])->validate();

        $settings = [
            'fields' => $args['fields'],
            'bank_fields' => $args['bank_fields'],
            'date_format' => $args['date_format']
        ];

        return BankSetting::create([
            'name' => $args['name'],
            'settings' => json_encode($settings)
        ]);
    }
}
