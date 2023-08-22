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

        if (+$order->total_owing !== 0.0) {
            throw new UnprocessableException(
                'Pendência financeira',
                'O pedido possui pendência financeira'
            );
        }

        $order->update([
            'closed_at' => $order->closed_at ? null : now()
        ]);

        if ($order->closed_at) {
            $this->saveFinalStatus($order);
        }

        return $order;
    }

    public function saveFinalStatus(Order $order): void
    {
        $data = $order->linkedStatus->map(function ($status) {
            return [
                'id' => $status->id,
                'text' => $status->text,
                'order' => $status->order,
                'sector' => $status->sector,
                'pivot' => [
                    'user' => $status->pivot->user,
                    'status' => $status->pivot->status,
                    'is_confirmed' => $status->pivot->is_confirmed,
                    'confirmed_at' => $status->pivot->confirmed_at,
                ]
            ];
        });

        activity()->withoutLogs(function () use ($order, $data) {
            $order->update([
                'final_status' => $data->toJson()
            ]);
        });
    }
}
