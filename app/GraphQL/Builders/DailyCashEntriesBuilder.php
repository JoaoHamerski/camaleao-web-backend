<?php

namespace App\GraphQL\Builders;

use Carbon\Carbon;
use App\Util\Helper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Query\Builder;
use App\GraphQL\Traits\PaymentsExpensesQueryTrait;

class DailyCashEntriesBuilder
{
    use PaymentsExpensesQueryTrait;

    public function __invoke($root, array $args)
    {
        $payments = $this->paymentsQuery();
        $expenses = $this->expensesQuery();

        $query = Helper::mergeQueries($expenses, $payments);

        $date = $this->getDate($args);

        $query->whereDate('created_at', $date)
            ->where(function ($query) {
                if (!Auth::user()->hasRole('GERENCIA')) {
                    $query->where('user_id', Auth::id());
                }
            });

        return $query;
    }

    public function getDate(array $args)
    {
        $date = Helper::filled($args, 'created_at')
            ? Carbon::createFromFormat('Y-m-d', $args['created_at'])
            : Carbon::now();

        return $date->toDateString();
    }
}
