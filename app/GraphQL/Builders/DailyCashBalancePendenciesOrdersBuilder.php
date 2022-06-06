<?php

namespace App\GraphQL\Builders;

use Carbon\Carbon;
use App\GraphQL\Queries\DailyCashBalance;

class DailyCashBalancePendenciesOrdersBuilder
{
    public function __invoke($root, array $args)
    {
        $date = $args['month'] === 'current'
            ? Carbon::now()
            : Carbon::now()->subMonthNoOverflow();

        $orders = DailyCashBalance::getTotalOwingOfMonthQuery($date, 'print_date');

        return $orders;
    }
}
