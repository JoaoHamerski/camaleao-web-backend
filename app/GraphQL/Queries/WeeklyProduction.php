<?php

namespace App\GraphQL\Queries;

use App\Models\Order;
use App\Util\Formatter;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class WeeklyProduction
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $data = $this->getFormattedData($args);
        $this->validator($data)->validate();

        $date = Carbon::createFromFormat('Y-m-d', $data['date']);
        $startOfWeek = $date->clone()->startOf('week');
        $endOfWeek = $date->clone()->endOf('week');

        $orders = $this->getOrdersBetweenDates($startOfWeek, $endOfWeek);

        return $this->getPopulatedWeekDays($orders, $startOfWeek);
    }

    public function getOrdersBetweenDates($startOfWeek, $endOfWeek)
    {
        return Order::whereBetween('production_date', [
            $startOfWeek->toDateString(),
            $endOfWeek->toDateString()
        ])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('production_date');
    }

    public function getPopulatedWeekDays($orders, $startOfWeek)
    {
        $daysOfWeek = [];

        for ($i = 0; $i < 6; $i++) {
            $date = $startOfWeek->clone()->addDays($i);
            $ordersOfDate = collect($orders->get($date->toDateString())) ?? [];

            $daysOfWeek[] = [
                'date' => $date->toDateString(),
                'orders' => $ordersOfDate,
                'total_quantity' => $ordersOfDate->sum('quantity')
            ];
        }

        return $daysOfWeek;
    }

    public function getFormattedData(array $data)
    {
        return (new Formatter($data))
            ->date('date')
            ->get();
    }

    public function validator(array $data)
    {
        return Validator::make($data, [
            'date' => ['required', 'date']
        ]);
    }
}
