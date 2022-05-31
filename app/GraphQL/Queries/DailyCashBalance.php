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

        $totalShirtsOnMonth = $this->getShirtsOnMonth(
            $date->clone(),
            'print_date'
        );

        $totalShirtsLastMonth = $this->getShirtsOnMonth(
            $date->clone()->subMonthNoOverflow(),
            'print_date'
        );

        $totalOwingOnMonth = $this->getTotalOwingOnMonth(
            $date->clone(),
            'print_date'
        );

        $totalOwingLastMonth = $this->getTotalOwingOnMonth(
            $date->clone()->subMonthNoOverflow(),
            'print_date'
        );

        return [
            'total_owing_on_month' => $totalOwingOnMonth,
            'total_owing_last_month' => $totalOwingLastMonth,
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
            ])
            ->sum('quantity');
    }

    public function getTotalOwingOnMonth(Carbon $month, string $field)
    {
        $totalOwing = DB::table('orders')
            ->whereBetween('print_date', [
                $month->clone()->startOf('month')->toDateString(),
                $month->clone()->endOf('month')->toDateString()
            ])
            ->sum('price');

        $totalPaid = DB::table('payments')
            ->join('orders', 'payments.order_id', '=', 'orders.id')
            ->whereBetween('print_date', [
                $month->clone()->startOf('month')->toDateString(),
                $month->clone()->endOf('month')->toDateString()
            ])
            ->whereNotNUll('payments.is_confirmed')
            ->sum('value');

        return bcsub($totalOwing, $totalPaid, 2);
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
