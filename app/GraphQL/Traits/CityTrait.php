<?php

namespace App\GraphQL\Traits;

trait CityTrait
{
    public function errorMessages()
    {
        return [
            'name.required' => __('validation.rules.required'),
            'state_id.required' => __('validation.rules.required_list', ['pronoun' => 'o'])
        ];
    }
}
