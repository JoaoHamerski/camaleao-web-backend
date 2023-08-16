<?php

namespace App\GraphQL\Queries;

use App\Models\City;
use App\Util\Helper;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

final class DashboardSalesAmountCities
{

    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'date' => ['required'],
        ])->validate();

        $query = $this->buildQuery();

        $this->queryDates($query, $args['date']);
        $this->querySelect($query);

        return $this->formatQueryResult($query);
    }

    public function formatQueryResult($query)
    {
        $cities = City::all();
        $result = $query->get();

        $result = $result->map(function ($cityAttrs) use ($cities) {
            return [
                'city' => $cities->first(fn ($_city) => $_city->id === $cityAttrs['id']),
                'amount' => data_get($cityAttrs, 'amount', 0),
                'shirts_count' => data_get($cityAttrs, 'shirts_count', 0),
                'orders_count' => data_get($cityAttrs, 'orders_count', 0)
            ];
        });

        $result = $result->sortByDesc([
            ['amount', 'desc'],
            ['city.name', 'asc']
        ]);

        return $result;
    }

    public function buildQuery()
    {
        return City::join(
            'clients',
            'cities.id',
            '=',
            'clients.city_id'
        )->join(
            'orders',
            'clients.id',
            '=',
            'orders.client_id'
        );
    }

    public function querySelect($query)
    {
        $query->groupBy('cities.id')->select([
            DB::raw('SUM(orders.quantity) AS shirts_count'),
            DB::raw('SUM(orders.price) AS amount'),
            DB::raw('COUNT(orders.id) AS orders_count'),
            'cities.*',
        ]);
    }

    public function getDates($date)
    {
        if (in_array($date, ['LAST_3_MONTHS', 'LAST_6_MONTHS'])) {
            return [
                Carbon::now()->subMonthsNoOverflow(
                    $date === 'LAST_3_MONTHS' ? 3 : 6
                )->startOfMonth(),
                Carbon::now()->subMonthNoOverflow()->endOfMonth()
            ];
        }

        if ($date === 'CURRENT_YEAR') {
            return [
                Carbon::now()->startOfYear(),
                Carbon::now()
            ];
        }

        return [
            Carbon::now()->createFromFormat('Y-m', $date)->startOfMonth(),
            Carbon::now()->createFromFormat('Y-m', $date)->endOfMonth()
        ];
    }

    public function queryDates($query, $date)
    {
        $query->whereBetween('orders.created_at', $this->getDates($date));
    }
}
