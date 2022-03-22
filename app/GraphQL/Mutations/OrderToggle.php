<?php

namespace App\GraphQL\Mutations;

use App\Models\Order;
use App\GraphQL\Exceptions\UnprocessableException;

class OrderToggle
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $order = Order::find($args['id']);

        $order->update([
            'closed_at' => $order->closed_at ? null : now()
        ]);

        return $order;
    }
}
