<?php

namespace App\GraphQL\Queries;

use App\Models\Order;
use Carbon\Carbon;

final class DashboardClientsSegmentation
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        $newClientsQuery = $this->getQuery('NEW_CLIENTS', $args['date']);
        $recurrentClientsQuery = $this->getQuery('RECURRENT_CLIENTS', $args['date']);

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

    public function queryDates($query, $date)
    {
        if ($date === 'month') {
            $query->whereBetween('orders.created_at', [
                Carbon::now()->startOfMonth()->toDateString(),
                Carbon::now()->endOfMonth()->toDateTimeString()
            ]);
        }

        if ($date === 'year') {
            $query->whereBetween('orders.created_at', [
                Carbon::now()->startOfMonth()->toDateString(),
                Carbon::now()->endOfMonth()->toDateTimeString()
            ]);
        }
    }

    public function getQuery($type, $date)
    {
        $query = Order::whereHas('client', function ($query) use ($type) {
            if ($type === 'NEW_CLIENTS') {
                $query->has('orders', '=', 1);
                return;
            }

            $query->has('orders', '>', 1);
        });

        $this->queryDates($query, $date);

        return $query;
    }
}
