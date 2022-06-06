<?php

namespace App\GraphQL\Queries;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Expense;
use Illuminate\Support\Facades\DB;
use App\GraphQL\Queries\DailyCashBalance;
use Illuminate\Support\Facades\Validator;

class DailyCashDetailedFlow
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'page' => ['required', 'numeric'],
            'date' => ['nullable', 'date_format:d/m/Y']
        ])->validate();

        $dates = $this->getDate($args);

        return $this->getDataOfMonths($dates);
    }

    public function getDataOfMonths($dates)
    {
        $DATE_FIELD = 'print_date';
        $data = [];

        foreach ($dates as $date) {
            $data[] = [
                'date' => $date->startOf('month')->toDateString(),
                'shirts_quantity' => DailyCashBalance::getShirtsOfMonth($date, $DATE_FIELD),
                'entry' => $this->getEntryData($date, $DATE_FIELD),
                'out' => $this->getOutData($date),
                'pendency' => DailyCashBalance::getTotalOwingOfMonthQuery($date, $DATE_FIELD)
                    ->sum('total_order_owing')
            ];
        }

        return $data;
    }

    public function getEntryData($date, $dateField)
    {
        $value = CashFlowBalance::paymentsQuery([
            'start_date' => $date->startOf('month')->toDateString(),
            'final_date' => $date->endOf('month')->toDateString()
        ])->where('is_confirmed', true)->sum('value');

        $ordersPriceAvg = Order::whereBetween($dateField, [
            $date->startOf('month')->toDateString(),
            $date->endOf('month')->toDateString()
        ])->avg('price');

        $unitiesAvg = Order::whereBetween($dateField, [
            $date->startOf('month')->toDateString(),
            $date->endOf('month')->toDateString()
        ])->selectRaw(
            'round(sum(price) / sum(quantity), 2) AS unities_avg'
        )->first()->unities_avg;

        return [
            'value' => number_format($value, 2, '.', ''),
            'orders_price_avg' => number_format($ordersPriceAvg, 2, '.', ''),
            'unities_avg' => number_format($unitiesAvg, 2, '.', '')
        ];
    }

    public function getOutData($date)
    {
        $value = CashFlowBalance::expensesQuery([
            'start_date' => $date->startOf('month')->toDateString(),
            'final_date' => $date->endOf('month')->toDateString()
        ])->where('is_confirmed', true)->sum('value');

        $data = DB::table('expense_types')
            ->leftJoin('expenses', 'expenses.expense_type_id', '=', 'expense_types.id')
            ->select([
                'expense_types.id',
                'name',
                DB::raw("IFNULL(SUM(value), 0) AS total")
            ])
            ->groupBy('expense_types.name')
            ->get();

        dd($data);
        return [
            'value' => number_format($value, 2, '.', '')
        ];
    }

    public function getDate($args)
    {
        $dates = [];

        if (isset($args['date'])) {
            return [Carbon::createFromFormat('d/m/Y', $args['date'])];
        }

        $date =  Carbon::now()->subMonths(($args['page'] - 1) * 6);

        for ($i = 0; $i < 6; $i++) {
            $dates[] = $date->clone()->subMonth($i);
        }

        return $dates;
    }
}
