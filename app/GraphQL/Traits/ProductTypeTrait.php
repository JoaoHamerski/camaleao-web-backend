<?php

namespace App\GraphQL\Traits;

use Illuminate\Support\Facades\Validator;

trait ProductTypeTrait
{
    private function validator($data)
    {
        return Validator::make($data, [
            'id' => ['sometimes', 'required', 'exists:product_types,id'],
            'name' => ['required', 'max:191']
        ], $this->errorMessages());
    }

    private function errorMessages()
    {
        return [
            'name.required' => __('validation.rules.required')
        ];
    }
}
