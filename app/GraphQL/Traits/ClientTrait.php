<?php

namespace App\GraphQL\Traits;

use App\Util\Formatter;
use Illuminate\Support\Facades\Validator;

trait ClientTrait
{
    private function getFormattedData(array $data)
    {
        return (new Formatter($data))
            ->name('name')
            ->stripNonDigits('phone')
            ->get();
    }

    private function validator($data, $isUpdate = false)
    {
        return Validator::make($data, [
            'id' => ['sometimes', 'required', 'exists:clients,id'],
            'name' => ['sometimes', 'required', 'max:191'],
            'phone' => ['nullable', 'min:8', 'max:11'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'shipping_company_id' => ['nullable', 'exists:shipping_companies,id'],
            'is_sponsor' => ['required', 'boolean']
        ], $this->errorMessages());
    }

    private function errorMessages()
    {
        return [
            'name.required' => __('validation.rules.required')
        ];
    }
}
