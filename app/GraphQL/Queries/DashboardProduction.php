<?php

namespace App\GraphQL\Queries;

use App\Models\AppConfig;
use App\Models\Order;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

final class DashboardProduction
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'production_date' => ['required', 'date_format:Y-m']
        ])->validate();

        $query = $this->query();

        return [
            'estampados' => $this->getDataByPeriods(
                static::confirmedStatusQuery($query->clone(), 'estampados')
            ),
            'costurados' => $this->getDataByPeriods(
                static::confirmedStatusQuery($query->clone(), 'costurados')
            ),
            'month_production' => $this->getData(
                static::monthProductionQuery($query->clone(), $args['production_date'])
            ),
            'late_orders' => $this->getData(
                static::lateOrdersQuery()
            ),
            'waiting_for_withdrawal_orders' => $this->getData(
                static::waitingForWithdrawalQuery()
            )
        ];
    }

    public static function query()
    {
        return Order::join('order_status', 'orders.id', '=', 'order_status.order_id')
            ->select(['orders.*']);
    }

    public static function queryPeriods($query)
    {
        $periods = [
            'day' => Carbon::now()->toDateString(),
            'week' => [
                Carbon::now()->startOfWeek()->toDateString(),
                Carbon::now()->endOfWeek()->toDateTimeString()
            ],
            'last_week' => [
                Carbon::now()->subWeek()->startOfWeek()->toDateString(),
                Carbon::now()->subWeek()->endOfWeek()->toDateTimeString()
            ]
        ];

        return [
            'day' => $query->clone()->whereDate('order_status.confirmed_at', $periods['day']),
            'week' => $query->clone()->whereBetween('order_status.confirmed_at', $periods['week']),
            'last_week' => $query->clone()->whereBetween('order_status.confirmed_at', $periods['last_week'])
        ];
    }

    public function getData($query)
    {
        return [
            'orders_count' => $query->count(),
            'shirts_count' => $query->sum('quantity'),
            'receipt' => $query->sum('price')
        ];
    }

    public function getDataByPeriods($query)
    {
        $queryPeriods = $this->queryPeriods($query);

        return [
            'day' => $this->getData($queryPeriods['day']),
            'week' => $this->getData($queryPeriods['week']),
            'last_week' => $this->getData($queryPeriods['last_week'])
        ];
    }

    public static function confirmedStatusQuery($query, $type)
    {
        $status = json_decode(AppConfig::get('dashboard', 'production'), true);

        $types = [
            'estampados' => $status['estampados_no_dia_id'],
            'costurados' => $status['costurados_no_dia_id'],
            'month_production' => $status['month_production_id']
        ];

        return $query
            ->where('order_status.status_id', $types[$type])
            ->where('order_status.is_confirmed', '=', 1)
            ->whereNotNull('order_status.confirmed_at');
    }

    public static function monthProductionQuery($query, $date)
    {
        $queryPeriods = [
            Carbon::createFromFormat('Y-m', $date)->startOfMonth()->toDateString(),
            Carbon::createFromFormat('Y-m', $date)->endOfMonth()->toDateTimeString()
        ];

        return static::confirmedStatusQuery($query, 'month_production')
            ->whereBetween('order_status.confirmed_at', $queryPeriods);
    }

    public static function lateOrdersQuery()
    {
        $statusId = json_decode(AppConfig::get('dashboard', 'production'), true)['late_orders_id'];

        return Order::whereHas('linkedStatus', function ($query) use ($statusId) {
            $query->where('order_status.status_id', $statusId)
                ->whereNotNull('order_status.confirmed_at');
        })
            ->where('created_at', '>', '2023-01-01')
            ->where('delivery_date', '<', Carbon::now()->toDateString());
    }

    public static function waitingForWithdrawalQuery()
    {
        $status = json_decode(AppConfig::get('dashboard', 'production'), true);
        $waitingForWithdrawalId = $status['waiting_for_withdrawal_id'];
        $deliveredId = $status['delivered_id'];

        return Order::whereExists(
            fn ($query) => $query
                ->selectRaw("1 FROM order_status")
                ->whereRaw("
                    orders.id = order_status.order_id
                    AND order_status.status_id = {$waitingForWithdrawalId}
                ")
        )->whereNotExists(
            fn ($query) => $query
                ->selectRaw('1 FROM order_status')
                ->whereRaw("
                    orders.id = order_status.order_id
                    AND order_status.status_id = {$deliveredId}
                ")
        );
    }
}
