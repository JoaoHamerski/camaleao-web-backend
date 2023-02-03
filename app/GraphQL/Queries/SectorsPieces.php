<?php

namespace App\GraphQL\Queries;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Sector;
use Illuminate\Support\Facades\DB;

class SectorsPieces
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
            'pieces' => $this->getSectorPieces($sector)
        ]);
    }

    private function getSectorPieces($sector)
    {
        return [
            'day' => $this->getPiecesOfDate('day', $sector),
            'week' => $this->getPiecesOfDate('week', $sector),
            'month' => $this->getPiecesOfDate('month', $sector),
        ];
    }

    private function getPiecesOfDate(string $date, $sector)
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

        $whereMethod = $this->getWhereMethod($date);

        return [
            'current_orders' => $ordersQuery
                ->clone()
                ->$whereMethod('order_status.created_at', $this->getWhereParams($date))
                ->orderBy('orders.created_at', 'desc')
                ->distinct(['orders.id']),
            'current' => $this->ordersQuantityBuilder(
                $whereMethod,
                $this->getWhereParams($date),
                $status
            ),
            'previous' => $this->ordersQuantityBuilder(
                $whereMethod,
                $this->getWhereParamsPrevious($date),
                $status
            )
        ];
    }

    private function ordersQuantityBuilder($whereMethod, $whereParams, $status)
    {
        return Order::whereHas(
            'concludedStatus',
            function ($query) use ($whereMethod, $whereParams, $status) {
                $query
                    ->$whereMethod('order_status.created_at', $whereParams)
                    ->whereIn('order_status.status_id', $status->pluck('id'));
            }
        )->sum('quantity');
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
