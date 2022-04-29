<?php

namespace App\GraphQL\Queries;

use Carbon\Carbon;
use App\Models\Expense;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Builder;
use App\GraphQL\Traits\PaymentsExpensesQueryTrait;

class DailyCashBalance
{
    use PaymentsExpensesQueryTrait;

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
            'balance_of_month' => $this->getBalanceOfMonth($payments, $expenses)
        ];
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
            ->whereBetween('created_at', [$startDate, $endDate]);

        $expensesBetweenDates = $expenses
            ->whereBetween('created_at', [$startDate, $endDate]);

        return $this->getBalance($paymentsBetweenDates, $expensesBetweenDates);
    }

    public function getBalanceOfDay(Builder $payments, Builder $expenses)
    {
        $paymentsOfDay = $payments
            ->clone()
            ->whereDate('created_at', Carbon::now()->toDateString());

        $expensesOfDay = $expenses
            ->clone()
            ->whereDate('created_at', Carbon::now()->toDateString());

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
