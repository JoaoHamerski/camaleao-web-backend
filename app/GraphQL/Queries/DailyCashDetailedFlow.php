<?php

namespace App\GraphQL\Queries;

use Carbon\Carbon;
use App\Util\Helper;
use App\Models\Order;
use App\Models\Payment;
use App\Models\AppConfig;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\GraphQL\Queries\DailyCashBalance;
use Illuminate\Support\Facades\Validator;

class DailyCashDetailedFlow
{
    /**
     * @var
     * Data para ser baseado todo o fluxo de dados buscado.
     */
    protected static $DATE_FIELD = 'orders.created_at';

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
        $data = [];
        $isGerencia = Auth::user()->hasRole('GERENCIA');

        foreach ($dates as $date) {
            if ($isGerencia) {
                $data[] = [
                    'date' => $date->startOf('month')->toDateString(),
                    'total_price' => $this->getTotalPriceOfMonth($date, self::$DATE_FIELD),
                    'shirts_total' => DailyCashBalance::getShirtsOfMonth($date, self::$DATE_FIELD),
                    'entry' => $this->getEntryData($date, self::$DATE_FIELD),
                    'out' => $this->getOutData($date),
                    'pendency' => DailyCashBalance::getTotalOwingOfMonthQuery($date, self::$DATE_FIELD)
                        ->sum('total_order_owing'),
                    'shirts_details' => $this->getShirtsDetailsOfMonth($date, self::$DATE_FIELD)
                ];
            }
        }

        return $data;
    }

    public function getShirtsDetails($query)
    {
        return [
            'count' => $query->count(),
            'quantity' => $query->sum('quantity'),
            'value' => $query->sum(DB::raw('ROUND(price, 2)'))
        ];
    }

    public function getShirtsDetailsOfMonth($date, $dateField)
    {
        $orderQuery = Order::whereBetween($dateField, [
            $date->startOfMonth()->toDateTimeString(),
            $date->endOfMonth()->toDateTimeString()
        ]);

        $lessThanFiveQuery = $orderQuery->clone()->where('quantity', '<', 5);
        $betweenFiveAndTenQuery = $orderQuery->clone()->whereBetween('quantity', [5, 10]);
        $moreThanTenQuery = $orderQuery->clone()->where('quantity', '>', 10);

        return [
            'less_than_five' => $this->getShirtsDetails($lessThanFiveQuery),
            'between_five_and_ten' => $this->getShirtsDetails($betweenFiveAndTenQuery),
            'more_than_ten' => $this->getShirtsDetails($moreThanTenQuery)
        ];
    }

    public function getTotalPriceOfMonth($date, $dateField)
    {
        $total = Order::query()->whereBetween($dateField, [
            $date->startOfMonth()->toDateTimeString(),
            $date->endOfMonth()->toDateTimeString(),
        ])->sum('price');

        return number_format($total, 2, '.', '');
    }

    public function getEntryData($date, $dateField)
    {
        $value = Payment::query()
            ->where('is_confirmed', true)
            ->whereBetween('date', [
                $date->startOfMonth()->toDateTimeString(),
                $date->endOfMonth()->toDateTimeString()
            ])->sum('value');

        $ordersPriceAvg = Order::whereBetween($dateField, [
            $date->startOfMonth()->toDateTimeString(),
            $date->endOfMonth()->toDateTimeString(),
        ])->avg('price');

        $unitiesAvg = Order::whereBetween($dateField, [
            $date->startOfMonth()->toDateTimeString(),
            $date->endOfMonth()->toDateTimeString()
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
            'start_date' => $date->startOf('month')->toDateTimeString(),
            'final_date' => $date->endOf('month')->toDateTimeString()
        ])->where('is_confirmed', true)->sum('value');

        $expensesByGroup = DB::table('expense_types')
            ->leftJoin('expenses', 'expenses.expense_type_id', '=', 'expense_types.id')
            ->whereBetween('expenses.date', [
                $date->startOfMonth()->toDateTimeString(),
                $date->endOfMonth()->toDateTimeString()
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
        $date = '01/' . $args['date'];

        if (Helper::filled($args, 'date')) {
            return [Carbon::createFromFormat('d/m/Y', $date)];
        }

        $date =  Carbon::now()->subMonthsNoOverflow(($args['page'] - 1) * 6);

        for ($i = 0; $i < 6; $i++) {
            $dates[] = $date->clone()->subMonthsNoOverflow($i);
        }

        return $dates;
    }
}
