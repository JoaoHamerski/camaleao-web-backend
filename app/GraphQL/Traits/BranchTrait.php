<?php

namespace App\GraphQL\Traits;

use Illuminate\Support\Facades\Validator;

trait BranchTrait
{
    public function validator($data)
    {
        return Validator::make($data, [
            'id' => ['sometimes', 'exists:branches,id'],
            'city_id' => ['required', 'exists:cities,id'],
            'shipping_company_id' => ['required', 'exists:shipping_companies,id'],
            'cities_id' => ['required', 'array'],
            'cities_id.*' => ['required', 'exists:cities,id']
        ], $this->errorMessages());
    }

    public function errorMessages()
    {
        return [
            'city_id.required' => __('validation.rules.required_list', [
                'pronoun' => 'uma'
            ]),
            'shipping_company_id.required' => __('validation.rules.required_list', [
                'pronoun' => 'uma'
            ]),
            'cities_id.required' => __('validation.rules.required_list', [
                'pronoun' => 'alguma'
            ])
        ];
    }
}
