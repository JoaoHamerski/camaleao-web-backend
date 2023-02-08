<?php

namespace App\GraphQL\Mutations;

use Exception;
use App\Models\Order;

class StatusSync
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        try {
            $this->syncOrders();

            return true;
        } catch (Exception $e) {
            return false;
        }

        return true;
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
