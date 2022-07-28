<?php

namespace App\GraphQL\Builders;

use App\Util\Helper;
use Illuminate\Support\Facades\Auth;
use App\GraphQL\Traits\PaymentsExpensesQueryTrait;
use Carbon\Carbon;

class DailyCashEntriesBuilder
{
    use PaymentsExpensesQueryTrait;

    public function __invoke($root, array $args)
    {
        $payments = $this->paymentsQuery();
        $expenses = $this->expensesQuery();

        $query = $this->mergePaymentsExpensesQueries($payments, $expenses);

        if (Helper::filled($args, 'created_at')) {
            $date = Carbon::createFromFormat('Y-m-d', $args['created_at']);
            $currentDate = $date->clone()->toDateString();
            $nextDate = $date->clone()->addDay()->toDateString();

            $query->whereRaw("date BETWEEN '$currentDate 00:00:00' AND '$currentDate 23:59:59'");
        }

        return $query;

        if (!Auth::user()->hasRole('gerencia')) {
            return $query
                ->where('user_id', Auth::id())
                ->whereNull('user_id');
        }

        return $query;
    }
}
