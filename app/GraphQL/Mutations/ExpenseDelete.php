<?php

namespace App\GraphQL\Mutations;

use App\Models\Expense;
use Illuminate\Support\Facades\Validator;

class ExpenseDelete
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $this->validator($args)->validate();

        $expense = Expense::find($args['id']);
        $expense->delete();

        return $expense;
    }

    public function validator(array $data)
    {
        return Validator::make($data, [
            'id' => ['required', 'exists:expenses,id'],
            'password' => ['required', 'current_password']
        ]);
    }
}
