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
            'city_id.required' => __('general.validation.shipping_companies.city_id_required'),
            'shipping_company_id.required' => __('general.validation.shipping_companies.shipping_company_id_required'),
            'cities_id.required' => __('general.validation.shipping_companies.cities_id_required')
        ];
    }
}
