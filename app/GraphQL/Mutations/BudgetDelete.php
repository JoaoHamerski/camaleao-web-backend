<?php

namespace App\GraphQL\Mutations;

use App\Models\Budget;
use Illuminate\Support\Facades\Validator;

class BudgetDelete
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'id' => ['required', 'exists:budgets']
        ])->validate();

        $budget = Budget::find($args['id']);
        $budget->delete();

        return $budget;
    }
}
