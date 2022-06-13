<?php

namespace App\GraphQL\Queries;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Expense;
use Illuminate\Support\Facades\DB;
use App\GraphQL\Queries\DailyCashBalance;
use App\Models\AppConfig;
use App\Models\Payment;
use App\Util\Helper;
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
            'date' => ['nullable', 'date_format:m/Y']
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
                'total_price' => $this->getTotalPriceOfMonth($date, $DATE_FIELD),
                'shirts_quantity' => DailyCashBalance::getShirtsOfMonth($date, $DATE_FIELD),
                'entry' => $this->getEntryData($date, $DATE_FIELD),
                'out' => $this->getOutData($date),
                'pendency' => DailyCashBalance::getTotalOwingOfMonthQuery($date, $DATE_FIELD)
                    ->sum('total_order_owing')
            ];
        }

        return $data;
    }

    public function getTotalPriceOfMonth($date, $dateField)
    {
        $total = Order::query()->whereBetween($dateField, [
            $date->startOfMonth()->toDateString(),
            $date->endOfMonth()->toDateString(),
        ])->sum('price');

        return number_format($total, 2, '.', '');
    }

    public function getEntryData($date, $dateField)
    {
        $value = Payment::query()
            ->where('is_confirmed', true)
            ->whereHas(
                'order',
                function ($query) use ($dateField, $date) {
                    $query->whereBetween($dateField, [
                        $date->startOfMonth()->toDateString(),
                        $date->endOfMonth()->toDateString()
                    ]);
                }
            )->sum('value');

        $ordersPriceAvg = Order::whereBetween($dateField, [
            $date->startOfMonth()->toDateString(),
            $date->endOfMonth()->toDateString(),
        ])->avg('price');

        $unitiesAvg = Order::whereBetween($dateField, [
            $date->startOfMonth()->toDateString(),
            $date->endOfMonth()->toDateString()
        ])->selectRaw(
            'round(sum(price) / sum(quantity), 2) AS unities_avg'
        )->first()->unities_avg;

        return [
            'total' => number_format($value, 2, '.', ''),
            'orders_price_avg' => number_format($ordersPriceAvg, 2, '.', ''),
            'unities_avg' => number_format($unitiesAvg, 2, '.', '')
        ];
    }

    public function getOutData($date)
    {
        $IDS_TO_SHOW_INDIVIDUALLY = AppConfig::get('app', 'expense_types_ids_to_show');

        $total = CashFlowBalance::expensesQuery([
            'start_date' => $date->startOf('month')->toDateString(),
            'final_date' => $date->endOf('month')->toDateString()
        ])->where('is_confirmed', true)->sum('value');

        $expensesByGroup = DB::table('expense_types')
            ->leftJoin('expenses', 'expenses.expense_type_id', '=', 'expense_types.id')
            ->whereBetween('expenses.date', [
                $date->startOfMonth()->toDateString(),
                $date->endOfMonth()->toDateString()
            ])
            ->where('expenses.is_confirmed', true)
            ->select([
                'expense_types.id',
                'expense_types.name',
                DB::raw("IFNULL(ROUND(SUM(expenses.value), 2), 0) AS total")
            ])
            ->groupBy('expense_types.id')
            ->get();

        $expenses = $expensesByGroup->filter(
            fn ($item) => in_array($item->id, $IDS_TO_SHOW_INDIVIDUALLY)
        );

        $otherExpenses = [[
            'name' => 'Outros',
            'total' => number_format($expensesByGroup->filter(
                fn ($item) => !in_array($item->id, $IDS_TO_SHOW_INDIVIDUALLY)
            )->sum('total'), 2, '.', '')
        ]];

        return [
            'total' => number_format($total, 2, '.', ''),
            'expense_types' => $expenses->merge($otherExpenses)
        ];
    }

    public function getDate($args)
    {
        $dates = [];

        if (Helper::filled($args, 'date')) {
            return [Carbon::createFromFormat('m/Y', $args['date'])];
        }

        $date =  Carbon::now()->subMonths(($args['page'] - 1) * 6);

        for ($i = 0; $i < 6; $i++) {
            $dates[] = $date->clone()->subMonth($i);
        }

        return $dates;
    }
}
