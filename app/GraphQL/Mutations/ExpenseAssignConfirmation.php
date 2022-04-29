<?php

namespace App\GraphQL\Mutations;

use App\Models\Expense;
use Illuminate\Support\Facades\Validator;

class ExpenseAssignConfirmation
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'id' => ['required', 'exists:expenses,id'],
            'confirmation' => ['required', 'boolean']
        ])->validate();

        $expense = Expense::find($args['id']);

        $expense->update([
            'is_confirmed' => $args['confirmation'],
            'confirmed_at' => now()
        ]);

        return $expense;
    }
}
