<?php

namespace App\GraphQL\Queries;

use App\Models\Order;
use App\Models\Status;
use Illuminate\Support\Facades\Auth;

class AuthUserSectors
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {

        return Auth::user()
            ->sectors()
            ->ordered()
            ->get()
            ->map(function ($sector) {
                $ordersQuery = Order::getBySector($sector);
                $ordersPaymentsQuery = $ordersQuery
                    ->clone()
                    ->join('payments', 'payments.order_id', '=', 'orders.id');

                return [
                    'orders_count' => $ordersQuery->count(),
                    'quantity_count' => $ordersQuery->sum('quantity'),
                    'pendency_total' => bcsub(
                        $ordersQuery->sum('price'),
                        $ordersPaymentsQuery->sum('value'),
                        2
                    ),
                    'sector' => $sector,
                    'next_status' => Status::getNextStatus($sector->status->last())
                ];
            });
    }
}
