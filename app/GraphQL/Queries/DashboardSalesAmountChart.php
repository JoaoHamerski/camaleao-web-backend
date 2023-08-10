<?php

namespace App\GraphQL\Queries;

use App\Models\Order;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

final class DashboardSalesAmountChart
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        return [
            'current' => $this->getEachDayAmount('current'),
            'previous' => $this->getEachDayAmount('previous')
        ];
    }

    public function getDates($period)
    {
        if ($period === 'current') {
            return [
                Carbon::now()->startOfMonth()->toDateString(),
                Carbon::now()->endOfMonth()->toDateTimeString()
            ];
        }

        return [
            Carbon::now()->subMonthNoOverflow()->startOfMonth()->toDateString(),
            Carbon::now()->subMonthNoOverflow()->endOfMonth()->toDateTimeString()
        ];
    }

    public function getPeriodOfDates($period, $dates)
    {
        return collect(array_map(
            fn ($carbonDate) => $carbonDate->format('Y-m-d'),
            CarbonPeriod::create($dates[0], $dates[1])->toArray()
        ));
    }

    public function getEachDayAmount($period)
    {
        $dates = $this->getDates($period);
        $allDatesOfMonth = $this->getPeriodOfDates($period, $dates);

        $data = Order::whereBetween('created_at', $dates)
            ->groupBy(DB::raw('DATE(`created_at`)'))
            ->select([
                DB::raw('SUM(`price`) AS amount'),
                DB::raw('DATE(created_at) AS day')
            ])
            ->orderBy('created_at', 'ASC')
            ->get();

        return $allDatesOfMonth->map(function ($date) use ($data) {

            $amount = data_get($data->firstWhere('day', $date), 'amount', 0);
            return [
                'day' => $date,
                'amount' => $amount
            ];
        });
    }
}
