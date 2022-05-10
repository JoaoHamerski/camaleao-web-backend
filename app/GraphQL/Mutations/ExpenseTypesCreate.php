<?php

namespace App\GraphQL\Mutations;

use App\Models\ExpenseType;
use Illuminate\Support\Facades\Validator;

class ExpenseTypesCreate
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'name' => ['required', 'max:50']
        ], $this->errorMessages())->validate();

        return ExpenseType::create($args);
    }

    public function errorMessages()
    {
        return [
            'name.required' => __('validation.rules.required', ['attribute' => 'tipo de despesa'])
        ];
    }
}
