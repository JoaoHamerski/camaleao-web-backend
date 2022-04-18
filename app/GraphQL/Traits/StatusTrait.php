<?php

namespace App\GraphQL\Traits;

use Illuminate\Support\Facades\Validator;

trait StatusTrait
{
    private function errorMessages()
    {
        return [
            'text.required' => 'Por favor, informe um texto'
        ];
    }

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
}
