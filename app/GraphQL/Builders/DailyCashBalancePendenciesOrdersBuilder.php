<?php

namespace App\GraphQL\Builders;

use Carbon\Carbon;
use App\GraphQL\Queries\DailyCashBalance;
use Illuminate\Support\Facades\Validator;

class DailyCashBalancePendenciesOrdersBuilder
{
    public function __invoke($root, array $args)
    {
        Validator::make($args, [
            'date' => ['required', 'date_format:Y-m-d']
        ])->validate();

        $date = Carbon::createFromFormat('Y-m-d', $args['date']);

        $orders = DailyCashBalance::getTotalOwingOfMonthQuery($date, 'orders.created_at');

        return $orders;
    }
}
