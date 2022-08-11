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

        $date = $this->getCurrentDate($args);

        $query->whereRaw("created_at BETWEEN '$date 00:00:00' AND '$date 23:59:59'")
            ->where(function ($query) {
                if (!Auth::user()->hasRole('GERENCIA')) {
                    $query->where('user_id', '=', Auth::id())
                        ->orWhereNull('user_id');
                }
            });

        return $query;
    }

    public function getCurrentDate(array $args)
    {
        $date = Helper::filled($args, 'created_at')
            ? Carbon::createFromFormat('Y-m-d', $args['created_at'])
            : Carbon::now();

        return $date->toDateString();
    }
}
