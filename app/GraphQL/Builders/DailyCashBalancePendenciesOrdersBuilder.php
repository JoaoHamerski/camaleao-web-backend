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

        $orders = Order::join('payments', 'orders.id', '=', 'payments.order_id')
            ->whereBetween('orders.print_date', [
                $date->clone()->startOf('month')->toDateString(),
                $date->clone()->endOf('month')->toDateString()
            ])
            ->where('payments.is_confirmed', '=', true)
            ->groupBy('payments.order_id')
            ->havingRaw('total_payments_order <> orders.price')
            ->select(['orders.*', DB::raw('SUM(payments.value) AS total_payments_order')]);

        return $orders;
    }
}
