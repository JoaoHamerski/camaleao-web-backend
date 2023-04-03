<?php

namespace App\GraphQL\Queries;

use App\GraphQL\Traits\PaymentsExpensesQueryTrait;
use App\Util\Helper;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DailyCashPendencies
{
    use PaymentsExpensesQueryTrait;

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $payments = $this->paymentsQuery();
        $expenses = $this->expensesQuery();

        $builder = Helper::mergeQueries($expenses, $payments);

        $query = $builder->where(function ($query) {
            $query->whereNull('is_confirmed');
            $query->whereDate('created_at', '<', Carbon::now()->toDateString());
        });

        return $query->groupBy('created_at_entry')
            ->orderBy(DB::raw('DATE(created_at)'), 'desc')
            ->get([
                DB::raw('DATE(created_at) as created_at_entry'),
                DB::raw('COUNT(*) as total')
            ]);
    }
}
