<?php

namespace App\GraphQL\Traits;

use Illuminate\Support\Facades\Validator;

trait SectorTrait
{
    public function validator(array $data)
    {
        return Validator::make($data, [
            'id' => ['sometimes', 'required', 'exists:sectors,id'],
            'name' => ['required'],
            'users' => ['nullable', 'array'],
            'users.*' => ['required', 'exists:users,id'],
            'status' => ['required', 'array'],
            'status.*' => ['required', 'exists:status,id']
        ], $this->errorMessages());
    }

    private function errorMessages()
    {
        return [
            'name.required' => __('validation.rules.required'),
            'status.required' => __('validation.rules.required_list', ['pronoun' => 'um'])
        ];
    }
}
