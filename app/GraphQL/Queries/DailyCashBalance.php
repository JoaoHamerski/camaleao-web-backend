<?php

namespace App\GraphQL\Queries;

use Carbon\Carbon;
use App\Models\Expense;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Builder;
use App\GraphQL\Traits\PaymentsExpensesQueryTrait;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class DailyCashBalance
{
    use PaymentsExpensesQueryTrait;

    protected static $DATE_FIELD = 'date';

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $expenses = Expense::where('is_confirmed', true);
        $payments = Payment::where('is_confirmed', true);

        return [
            'balance_of_day' => $this->getBalanceOfDay($payments, $expenses),
            'balance_of_week' => $this->getBalanceOfWeek($payments, $expenses),
            'balance_of_month' => $this->getBalanceOfMonth($payments, $expenses),
            'pendency' => $this->getPendency()
        ];
    }

    public function getPendency()
    {
        $date = Carbon::now();

        $totalShirtsLastMonth = $this->getShirtsOnMonth(
            $date->clone()->subMonthNoOverflow(),
            'print_date'
        );

        $totalShirtsOnMonth = $this->getShirtsOnMonth(
            $date->clone(),
            'print_date'
        );

        $totalOwingOnMonth = $this->getTotalOwingOnMonthQuery(
            $date->clone(),
            'print_date'
        )->sum('total_order_owing');

        $totalOwingLastMonth = $this->getTotalOwingOnMonthQuery(
            $date->clone()->subMonthNoOverflow(),
            'print_date'
        )->sum('total_order_owing');

        return [
            'total_owing_on_month' => number_format($totalOwingOnMonth, 2, '.', ''),
            'total_owing_last_month' => number_format($totalOwingLastMonth, 2, '.', ''),
            'total_shirts_on_month' => $totalShirtsOnMonth,
            'total_shirts_last_month' => $totalShirtsLastMonth
        ];
    }

    public function getShirtsOnMonth(Carbon $month, string $field)
    {
        return Order::query()
            ->whereBetween($field, [
                $month->startOf('month')->toDateString(),
                $month->endOf('month')->toDateString()
            ])->sum('quantity');
    }

    public static function getTotalOwingOnMonthQuery(Carbon $month, string $field)
    {
        $confirmedPaymentsSubQuery = <<<STR
            SELECT SUM(`value`)
                FROM `payments`
                WHERE `is_confirmed` = 1
                AND orders.id = payments.order_id
        STR;

        return Order::leftJoin('payments', 'orders.id', '=', 'payments.order_id')
            ->whereBetween($field, [
                $month->startOf('month')->toDateString(),
                $month->endOf('month')->toDateString()
            ])
            ->groupBy('orders.id')
            ->havingRaw('total_payments_order <> orders.price')
            ->select([
                'orders.*',
                DB::raw("
                    IFNULL(($confirmedPaymentsSubQuery), 0) AS total_payments_order
                "),
                DB::raw("
                    IFNULL(orders.price, 0) - IFNULL(($confirmedPaymentsSubQuery), 0) AS total_order_owing
                ")
            ]);
    }

    public function getBalance(Builder $payments, Builder $expenses)
    {
        $entry = $payments->sum('value');
        $out = $expenses->sum('value');

        return [
            'entry' => number_format($entry, 2, '.', ''),
            'out' => number_format($out, 2, '.', ''),
            'balance' => bcsub($entry, $out, 2)
        ];
    }

    public function getBalanceBetweenDates(Builder $payments, Builder $expenses, $startDate, $endDate)
    {
        $paymentsBetweenDates = $payments
            ->whereBetween(self::$DATE_FIELD, [$startDate, $endDate]);

        $expensesBetweenDates = $expenses
            ->whereBetween(self::$DATE_FIELD, [$startDate, $endDate]);

        return $this->getBalance($paymentsBetweenDates, $expensesBetweenDates);
    }

    public function getBalanceOfDay(Builder $payments, Builder $expenses)
    {
        $paymentsOfDay = $payments
            ->clone()
            ->whereDate(self::$DATE_FIELD, Carbon::now()->toDateString());

        $expensesOfDay = $expenses
            ->clone()
            ->whereDate(self::$DATE_FIELD, Carbon::now()->toDateString());

        return $this->getBalance($paymentsOfDay, $expensesOfDay);
    }

    public function getBalanceOfWeek(Builder $payments, Builder $expenses)
    {
        return $this->getBalanceBetweenDates(
            $payments->clone(),
            $expenses->clone(),
            Carbon::now()->startOfWeek()->toDateString(),
            Carbon::now()->endOfWeek()->toDateString()
        );
    }

    public function getBalanceOfMonth(Builder $payments, Builder $expenses)
    {
        return $this->getBalanceBetweenDates(
            $payments->clone(),
            $expenses->clone(),
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        );
    }
}
