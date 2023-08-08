<?php

namespace App\GraphQL\Mutations;

use App\Models\Order;
use Illuminate\Support\Facades\Validator;

class OrderUpdateStatus
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'id' => ['required', 'exists:orders,id'],
            'status_id' => ['required', 'exists:status,id']
        ])->validate();

        $order = Order::find($args['id']);
        $order->update(['status_id' => $args['status_id']]);
        $order->syncStatus(true);

        return $order;
    }
}
