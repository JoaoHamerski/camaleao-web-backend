<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
use App\Models\AppConfig;
use Illuminate\Support\Facades\Validator;

class ChangeEmployeeExpenseField
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'id' => ['required', 'exists:users,id']
        ])->validate();

        AppConfig::set('app', 'employee_expense', $args['id']);

        return User::find($args['id']);
    }
}
