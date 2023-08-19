<?php

namespace App\GraphQL\Queries;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Sector;

class ProductionPanel
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $sectors = Sector::ordered()->get();

        return $sectors->map(fn ($sector) => [
            'sector' => $sector,
            'orders_on_periods' => $this->getOrdersOnPeriods($sector)
        ]);
    }

    private function getOrdersOnPeriods($sector)
    {
        return [
            'day' => $this->getDataByPeriod('day', $sector),
            'week' => $this->getDataByPeriod('week', $sector),
            'month' => $this->getDataByPeriod('month', $sector),
        ];
    }

    private function getDataByPeriod(string $date, $sector)
    {
        $status = $sector->status;

        $ordersQuery = Order::join(
            'order_status',
            'orders.id',
            '=',
            'order_status.order_id'
        )
            ->select('orders.*')
            ->whereIn('order_status.status_id', $status->pluck('id')->toArray());
        // ->where('order_status.is_confirmed', 1)
        // ->whereNotNull('order_status.confirmed_at');

        $whereMethod = $this->getWhereMethod($date);

        return [
            'current_orders' => $ordersQuery
                ->clone()
                ->$whereMethod('order_status.confirmed_at', $this->getWhereParams($date))
                ->orderBy('orders.created_at', 'desc')
                ->distinct(['orders.id']),
            'current_count' => $ordersQuery
                ->clone()
                ->$whereMethod('order_status.confirmed_at', $this->getWhereParams($date))
                ->distinct()
                ->sum('quantity'),
            'previous_count' => $ordersQuery
                ->clone()
                ->$whereMethod('order_status.confirmed_at', $this->getWhereParamsPrevious($date))
                ->distinct()
                ->sum('quantity')
        ];
    }

    private function getWhereMethod($date)
    {
        return $date === 'day'
            ? 'whereDate'
            : 'whereBetween';
    }

    private function getWhereParams($date)
    {
        return $date === 'day'
            ? Carbon::now()
            : [
                Carbon::now()->startOf($date),
                Carbon::now()->endOf($date)
            ];
    }

    private function getWhereParamsPrevious($date)
    {
        $subDateMethod = $date === 'week'
            ? 'subWeek'
            : 'subMonthNoOverflow';

        return $date === 'day'
            ? Carbon::now()->subDay()
            : [
                Carbon::now()->$subDateMethod()->startOf($date),
                Carbon::now()->$subDateMethod()->endOf($date),
            ];
    }
}
