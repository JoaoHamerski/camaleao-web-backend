<?php

namespace App\GraphQL\Queries;

use App\Models\Order;
use Carbon\Carbon;

final class DashboardSalesAmount
{
    static $PERIODS = [
        'CURRENT' => 'current',
        'PREVIOUS' => 'previous'
    ];

    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        return [
            'current' => $this->getSalesAmount(static::$PERIODS['CURRENT']),
            'previous' => $this->getSalesAmount(static::$PERIODS['PREVIOUS'])
        ];
    }

    public function getSalesAmount($period)
    {
        $dates = $this->getIntervalDates($period);

        return [
            'day' => $this->getOrderSalesAmountByDate($dates['day']),
            'week' => $this->getOrderSalesAmountByDate($dates['week']),
            'month' => $this->getOrderSalesAmountByDate($dates['month'])
        ];
    }

    public function getOrderSalesAmountByDate($date)
    {
        if (is_array($date)) {
            return Order::whereBetween('created_at', $date)->sum('price');
        }

        return Order::where('created_at', $date)->sum('price');
    }

    public function getIntervalDates($period)
    {
        $dates = [
            'current' => [
                'day' => Carbon::now()->toDateString(),
                'week' => [
                    Carbon::now()->startOfWeek()->toDateString(),
                    Carbon::now()->endOfWeek()->toDateTimeString()
                ],
                'month' => [
                    Carbon::now()->startOfMonth()->toDateString(),
                    Carbon::now()->endOfMonth()->toDateTimeString()
                ]
            ],
            'previous' => [
                'day' => Carbon::now()->subDay()->toDateString(),
                'week' => [
                    Carbon::now()->subWeek()->startOfWeek()->toDateString(),
                    Carbon::now()->subWeek()->endOfWeek()->toDateTimeString()
                ],
                'month' => [
                    Carbon::now()->subMonthNoOverflow()->startOfMonth()->toDateString(),
                    Carbon::now()->subMonthNoOverflow()->endOfMonth()->toDateTimeString()
                ]
            ]
        ];

        return $dates[$period];
    }
}
