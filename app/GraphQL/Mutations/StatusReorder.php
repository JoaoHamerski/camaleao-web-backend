<?php

namespace App\GraphQL\Mutations;

use App\Models\Order;
use App\Models\Status;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;

class StatusReorder
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'input.*.id' => ['required', 'exists:status,id'],
            'input.*.order' => ['required', 'numeric']
        ])->validate();

        $orders = $args['input'];

        foreach ($orders as $order) {
            Status::find($order['id'])->update(['order' => $order['order']]);
        }

        $this->syncOrders();

        return Status::orderBy('order')->get();
    }

    private function syncOrders()
    {
        activity()->withoutLogs(function () {
            $orders = Order::whereNull('closed_at')->get();

            $orders->each(function ($order) {
                $order->syncStatus();
            });
        });
    }
}
