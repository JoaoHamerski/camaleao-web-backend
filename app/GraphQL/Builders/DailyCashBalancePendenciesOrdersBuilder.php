<?php

namespace App\GraphQL\Builders;

use Carbon\Carbon;
use App\Models\Order;
use App\GraphQL\Queries\DailyCashBalance;
use Illuminate\Support\Facades\DB;

class DailyCashBalancePendenciesOrdersBuilder
{
    public function __invoke($root, array $args)
    {
        $date = $args['month'] === 'current'
            ? Carbon::now()
            : Carbon::now()->subMonthNoOverflow();

        $orders = DailyCashBalance::getTotalOwingOnMonthQuery($date, 'print_date');

        return $orders;
    }
}
