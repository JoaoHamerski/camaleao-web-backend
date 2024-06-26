<?php

namespace App\GraphQL\Traits;

use App\Util\Formatter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

trait ClientTrait
{
    public function getFormattedData(array $data)
    {
        return (new Formatter($data))
            ->name('name')
            ->stripNonDigits('phone')
            ->currencyBRL('bonus')
            ->get();
    }

    public function validator($data, $isUpdate = false)
    {
        return Validator::make($data, [
            'id' => ['sometimes', 'required', 'exists:clients,id'],
            'name' => ['sometimes', 'required', 'max:191'],
            'phone' => ['nullable', 'min:8', 'max:11'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'shipping_company_id' => ['nullable', 'exists:shipping_companies,id'],
            'recommended_client_id' => [
                'nullable',
                'exists:clients,id',
                isset($data['id']) ? Rule::notIn([$data['id']]) : ''
            ],
            'bonus' => ['nullable']
        ], $this->errorMessages());
    }

    public function errorMessages()
    {
        return [
            'name.required' => __('validation.rules.required')
        ];
    }
}
