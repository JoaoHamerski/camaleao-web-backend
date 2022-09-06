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

        if ($order->closed_at) {
            $this->consolidateConcludedStatus($order);
        }

        return $order;
    }

    public function consolidateConcludedStatus(Order $order): void
    {
        $data = $order->concludedStatus->map(function ($status) {
            return [
                'id' => $status->id,
                'text' => $status->text,
                'order' => $status->order,
                'sector' => $status->sector,
                'pivot' => [
                    'user' => $status->pivot->user,
                    'status' => $status->pivot->status,
                    'is_auto_concluded' => $status->pivot->is_auto_concluded,
                    'created_at' => $status->pivot->created_at->toDateTimeString(),
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
