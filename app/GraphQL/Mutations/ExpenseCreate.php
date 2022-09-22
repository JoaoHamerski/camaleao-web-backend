<?php

namespace App\GraphQL\Mutations;

use App\Traits\EntriesTrait;
use App\GraphQL\Traits\ExpenseTrait;
use Illuminate\Support\Facades\Auth;
use App\GraphQL\Exceptions\UnprocessableException;

class ExpenseCreate
{
    use EntriesTrait, ExpenseTrait;

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $data = $this->getFormattedData($args);

        $this->validator($data)->validate();

        if ($this->isFromEntries($data) && !$this->isValidEntry($data)) {
            throw new UnprocessableException(
                'Dados inválidos.',
                'Os dados informados diferem da entrada bancária.'
            );
        }

        $data = $this->handleFilesUpload($data);

        $data['employee_name'] = Auth::user()->name;

        $expense = Auth::user()->expenses()->create($data);

        if ($expense->isConfirmable($data)) {
            $expense->confirm();
        }

        return $expense;
    }
}
