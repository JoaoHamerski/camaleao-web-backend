<?php

namespace App\GraphQL\Builders;

use Illuminate\Support\Facades\Auth;
use App\GraphQL\Traits\PaymentsExpensesQueryTrait;

class DailyCashEntriesBuilder
{
    use PaymentsExpensesQueryTrait;

    public function __invoke()
    {
        $payments = $this->paymentsQuery();
        $expenses = $this->expensesQuery();

        $query = $this->mergePaymentsExpensesQueries($payments, $expenses);

        if (!Auth::user()->hasRole('gerencia')) {
            return $query->where('user_id', Auth::id())
                ->orWhereNull('user_id');
        }

        return $query;
    }
}
