<?php

namespace App\GraphQL\Mutations;

use App\Models\ExpenseType;
use Illuminate\Support\Facades\Validator;

class ExpenseTypesUpdate
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'id' => ['required', 'exists:expense_types,id'],
            'name' => ['nullable', 'max:50']
        ])->validate();

        $expense = ExpenseType::find($args['id']);

        $expense->update($args);

        return $expense;
    }
}
