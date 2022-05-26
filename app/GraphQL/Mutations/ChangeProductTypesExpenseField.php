<?php

namespace App\GraphQL\Mutations;

use App\Models\AppConfig;
use App\Models\ExpenseType;
use Illuminate\Support\Facades\Validator;

class ChangeProductTypesExpenseField
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'id' => ['required', 'exists:expense_types,id']
        ])->validate();

        AppConfig::set('app', 'product_types_expense', $args['id']);

        return ExpenseType::find($args['id']);
    }
}
