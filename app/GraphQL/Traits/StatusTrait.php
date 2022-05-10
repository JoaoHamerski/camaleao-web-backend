<?php

namespace App\GraphQL\Traits;

use Illuminate\Support\Facades\Validator;

trait StatusTrait
{
    public function validator($data, $isUpdate = false)
    {
        $rules = [
            'text' => ['required', 'max:191']
        ];

        if ($isUpdate) {
            $rules['id'] = ['required', 'exists:status,id'];
        }

        return Validator::make($data, $rules, $this->errorMessages());
    }

    private function errorMessages()
    {
        return [
            'text.required' => __('validation.rules.required', ['attribute' => 'nome do status'])
        ];
    }
}
