<?php

namespace App\GraphQL\Queries;

use App\Models\Activity;
use App\Models\Order;
use Illuminate\Support\Facades\Validator;

class OrdersActivities
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'id' => ['required', 'exists:orders']
        ])->validate();

        $order = Order::find($args['id']);
        $activities = collect();

        $queryOrder = Activity::where('subject_type', 'App\Models\Order')
            ->where('subject_id', $order->id);

        $queryPayments = Activity::where('subject_type', 'App\Models\Payment')
            ->whereIn('subject_id', $order->payments->pluck('id'));

        $query = $queryOrder->unionAll($queryPayments);

        $query->orderBy('created_at', 'DESC');

        return $query->get();
    }
}
