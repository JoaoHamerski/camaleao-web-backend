<?php

namespace App\GraphQL\Mutations;

use App\GraphQL\Traits\ExpenseTrait;
use App\Models\Expense;
use App\Util\Helper;

class ExpenseUpdate
{
    use ExpenseTrait;

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $expense = Expense::find($args['id']);

        $data = $this->getFormattedData($args, $expense);

        $this->validator($data, $expense)->validate();

        $data = $this->handleFilesUpload($data, $expense);

        $expense->update($data);

        return $expense;
    }
}
