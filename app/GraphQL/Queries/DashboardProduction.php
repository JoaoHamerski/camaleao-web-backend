<?php

namespace App\GraphQL\Queries;

use App\Models\Order;
use Carbon\Carbon;

final class DashboardProduction
{
    static $ESTAMPADOS_ID = '17';
    static $COSTURADOS_ID = '5';
    static $MONTH_PRODUCTION_STATUS_ID = '5';
    static $DISPONIVEL_PARA_RETIRADA_STATUS_ID = '7';
    static $COSTURADOS_E_EMBALADOS_ID = '5';
    static $ENTREGUE_STATUS_ID = '18';

    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        return [
            'estampados' => $this->getFormattedData(
                $this->buildQuery()
                    ->where('order_status.status_id', static::$ESTAMPADOS_ID)
            ),
            'costurados' => $this->getFormattedData(
                $this->buildQuery()
                    ->where('order_status.status_id', static::$COSTURADOS_ID)
            ),
            'month_production' => $this->getMonthProduction(),
            'late_orders' => $this->getLateOrders(),
            'waiting_for_withdrawal_orders' => $this->getWaitingForWithdrawalOrders()
        ];
    }

    public function getWaitingForWithdrawalOrders()
    {
        $disponivelParaRetiradaId = static::$DISPONIVEL_PARA_RETIRADA_STATUS_ID;
        $entregueId = static::$ENTREGUE_STATUS_ID;

        $query = Order::whereExists(
            fn ($query) => $query
                ->selectRaw('1 FROM order_status')
                ->whereRaw("
                    orders.id = order_status.order_id
                    AND order_status.status_id = $disponivelParaRetiradaId
               ")
        )
            ->whereNotExists(
                fn ($query) => $query
                    ->selectRaw('1 FROM order_status')
                    ->whereRaw("
                    orders.id = order_status.order_id
                    AND order_status.status_id = $entregueId
               ")
            );

        return $query->count();
    }

    public function getLateOrders()
    {
        $disponivelParaRetiradaId = static::$DISPONIVEL_PARA_RETIRADA_STATUS_ID;

        $query = Order::whereNotExists(function ($query) use ($disponivelParaRetiradaId) {
            $query->selectRaw('1 from order_status');
            $query->whereRaw(
                "orders.id = order_status.order_id AND order_status.status_id = {$disponivelParaRetiradaId}"
            );
        })->whereDate('delivery_date', '<', Carbon::now());

        return $query->count();
    }

    public function getMonthProduction()
    {
        $query = $this->buildQuery()
            ->where('order_status.status_id', static::$MONTH_PRODUCTION_STATUS_ID)
            ->whereBetween('order_status.created_at', [
                Carbon::now()->startOfMonth()->toDateString(),
                Carbon::now()->endOfMonth()->toDateTimeString()
            ]);

        return [
            'orders_count' => $query->count(),
            'receipt' => $query->sum('price')
        ];
    }

    public function getQueryDates($query)
    {
        return [
            'day' => $query->clone()
                ->whereDate('order_status.created_at', Carbon::now()->toDateString()),
            'week' => $query->clone()
                ->whereBetween(
                    'order_status.created_at',
                    [
                        Carbon::now()->startOfWeek()->toDateString(),
                        Carbon::now()->endOfWeek()->toDateTimeString()
                    ]
                ),
            'last_week' => $query->clone()
                ->whereBetween(
                    'order_status.created_at',
                    [
                        Carbon::now()->subWeek()->startOfWeek()->toDateString(),
                        Carbon::now()->subWeek()->endOfWeek()->toDateTimeString()
                    ]
                )
        ];
    }

    public function getDataByDates($queryDates, $type)
    {
        if ($type === 'count') {
            return [
                'day' => $queryDates['day']->count(),
                'week' => $queryDates['week']->count(),
                'last_week' => $queryDates['last_week']->count()
            ];
        }

        if ($type === 'receipt') {
            return [
                'day' => $queryDates['day']->sum('price'),
                'week' => $queryDates['week']->sum('price'),
                'last_week' => $queryDates['last_week']->sum('price'),
            ];
        }

        return null;
    }

    public function getFormattedData($query)
    {
        $queryDates = $this->getQueryDates($query);

        return [
            'orders_count' => $this->getDataByDates($queryDates, 'count'),
            'receipt' => $this->getDataByDates($queryDates, 'receipt')
        ];
    }

    public function buildQuery()
    {
        return Order::join(
            'order_status',
            'orders.id',
            '=',
            'order_status.order_id'
        );
    }
}
