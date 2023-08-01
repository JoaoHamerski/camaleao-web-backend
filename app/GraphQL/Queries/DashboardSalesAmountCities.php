<?php

namespace App\GraphQL\Queries;

use App\Models\City;
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
            'month' => [Rule::in(['current', 'previous'])]
        ])->validate();

        $query = $this->buildQuery();

        $this->queryDates($query, $args['month']);
        $this->querySelect($query);

        return $this->formatQueryResult($query);
    }

    public function formatQueryResult($query)
    {
        $cities = City::all();
        $result = $query->get();

        $result = $cities->map(function ($city) use ($result) {
            $cityAttrs = $result->first(fn ($_city) => $_city->id === $city->id);

            return [
                'city' => $city,
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

    public function getQueryDates($month)
    {
        if ($month === 'current') {
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

    public function queryDates($query, $month)
    {
        $query->whereBetween('orders.created_at', $this->getQueryDates($month));
    }
}
