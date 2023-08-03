<?php

namespace App\GraphQL\Queries;

use App\Models\Order;

final class DashboardClientsSegmentation
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        $newClientsQuery = $this->getQuery('NEW_CLIENTS');
        $recurrentClientsQuery = $this->getQuery('RECURRENT_CLIENTS');

        return [
            'new_clients' => $this->getFormattedData($newClientsQuery),
            'recurrent_clients' => $this->getFormattedData($recurrentClientsQuery)
        ];
    }

    public function getFormattedData($query)
    {
        return [
            'amount' => $query->sum('price'),
            'amount_pre_registered' => $query->clone()->whereNull('quantity')->sum('price'),
            'shirts_count' => $query->sum('quantity'),
            'orders_count' => $query->count(),
            'orders_count_pre_registered' => $query->clone()->whereNull('quantity')->count()
        ];
    }

    public function getQuery($type)
    {
        if ($type == 'NEW_CLIENTS') {
            return Order::whereHas('client', function ($query) {
                $query->has('orders', '=', 1);
            });
        }

        return Order::whereHas('client', function ($query) {
            $query->has('orders', '>', 1);
        });
    }
}
