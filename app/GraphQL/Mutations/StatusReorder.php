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

        $this->syncOrdersStatus();

        return Status::orderBy('order')->get();
    }


    public function syncOrdersStatus()
    {
        $orders = Order::whereNull('closed_at');

        activity()->withoutLogs(function () use ($orders) {
            $orders->each(function ($order) {
                $order->syncStatus();
            });
        });
    }
}
