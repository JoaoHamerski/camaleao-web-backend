<?php

namespace App\GraphQL\Mutations;

use App\Util\Helper;
use App\Models\BankSetting;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class BankSettingsCreate
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $this->validator($args)->validate();

        $settings = [
            'fields' => $args['fields'],
            'bank_fields' => $args['bank_fields'],
            'date_format' => $args['date_format'],
            'via_id' => Helper::filled($args, 'via_id')
                ? $args['via_id']
                : null
        ];

        return BankSetting::create([
            'name' => $args['name'],
            'settings' => json_encode($settings)
        ]);
    }

    private function validator(array $data)
    {
        $validDateFormats = [
            'dd/mm/yyyy',
            'mm/dd/yyyy',
            'yyyy/dd/mm',
            'yyyy/mm/dd'
        ];

        return Validator::make($data, [
            'name' => ['required'],
            'date_format' => ['required', Rule::in($validDateFormats)],
            'fields.*' => ['required', 'string'],
            'fields.description' => ['nullable', 'string'],
            'bank_fields' => ['required', 'array'],
            'bank_fields.*' => ['required', 'string'],
            'via_id' => ['nullable', 'exists:vias,id']
        ], $this->errorMessages());
    }

    private function errorMessages()
    {
        return [
            'name.required' => __('validation.rules.required'),
            'fields.*.required' => __('validation.rules.required_list')
        ];
    }
}
