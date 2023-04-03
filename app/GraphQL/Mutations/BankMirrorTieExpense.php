<?php

namespace App\GraphQL\Mutations;

use App\Models\Entry;
use App\Models\Expense;
use App\GraphQL\Traits\ExpenseTrait;
use Illuminate\Support\Facades\Auth;

class BankMirrorTieExpense
{
    use ExpenseTrait;

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $data = $this->getFormattedData($args);

        $expense = Expense::where('bank_uid', $args['bank_uid'])->first();

        $this->validator($data, $expense)->validate();

        $data = $this->handleFilesUpload($data);

        return $this->handleExpenseTie($data, $expense);
    }

    public function handleExpenseTie($data, $expense = null)
    {
        $entry = Entry::where('bank_uid', $data['bank_uid'])->first();

        if (!$expense) {
            $this->createExpense(array_merge($data, [
                'employee_name' => Auth::user()->name
            ]));

            return $entry;
        }

        $this->updateExpense($data, $expense);

        return Entry::getModEntryForCache($entry);
    }

    public function createExpense($data)
    {
        $expense = Auth::user()->expenses()->make($data);

        $expense->saveQuietly();
    }

    public function updateExpense($data, $expense)
    {
        $expense->update($data);
    }
}
