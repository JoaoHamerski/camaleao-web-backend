<?php

namespace App\GraphQL\Mutations;

use App\GraphQL\Traits\ExpenseTrait;
use Illuminate\Support\Facades\Auth;

class ExpenseCreate
{
    use ExpenseTrait;

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $data = $this->getFormattedData($args);

        $this->validator($data)->validate();

        $data = $this->handleFilesUpload($data);

        $data['employee_name'] = Auth::user()->name;

        $expense = Auth::user()->expenses()->create($data);

        if (Auth::user()->hasRole('gerencia')) {
            $expense->confirm();
        }

        return $expense;
    }
}
