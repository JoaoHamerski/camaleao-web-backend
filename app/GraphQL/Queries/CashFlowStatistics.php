<?php

namespace App\GraphQL\Queries;

use App\Models\Order;
use App\Models\Expense;
use App\Util\Formatter;
use Illuminate\Support\Facades\Validator;
use App\Models\Payment;
use App\Util\Helper;

class CashFlowStatistics
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $data = $this->getFormattedData($args);
        $this->validator($data)->validate();

        $ordersCreatedQuery = $this->getOrdersCreatedQuery($data);
        $ordersClosedQuery = $this->getOrdersClosedQuery($ordersCreatedQuery->clone());
        $ordersWithNoPaymentsQuery = $this->getOrdersWithNoPaymentsQuery($ordersCreatedQuery->clone());
        $balance = $this->evaluateBalance($data);

        return [
            'created_orders' => $ordersCreatedQuery->count(),
            'created_shirts' => $ordersCreatedQuery->sum('quantity'),
            'closed_orders' => $ordersClosedQuery->count(),
            'closed_shirts' => $ordersClosedQuery->sum('quantity'),
            'no_payments_orders' => $ordersWithNoPaymentsQuery->count(),
            'no_payments_shirts' => $ordersWithNoPaymentsQuery->sum('quantity'),
            'balance' => $balance
        ];
    }

    public function expensesQuery($data)
    {
        if (Helper::filledAll($data, ['start_date', 'final_date'])) {
            return Expense::whereBetween('date', [
                $data['start_date'],
                $data['final_date']
            ]);
        }

        if (Helper::filled($data, 'start_date')) {
            return Expense::whereDate('date', $data['start_date']);
        }

        return Expense::query();
    }

    public function paymentsQuery($data)
    {
        if (Helper::filledAll($data, ['start_date', 'final_date'])) {
            return Payment::whereBetween('date', [
                $data['start_date'],
                $data['final_date']
            ]);
        }

        if (Helper::filled($data, 'start_date')) {
            return Payment::whereDate('date', $data['start_date']);
        }

        return Payment::query();
    }

    public function evaluateBalance($data)
    {
        $totalExpensesValue = $this->expensesQuery($data)->sum('value');
        $totalPaymentsValue = $this->paymentsQuery($data)->sum('value');

        return bcsub($totalPaymentsValue, $totalExpensesValue, 2);
    }

    public function getOrdersCreatedQuery($data)
    {
        if (Helper::filledAll($data, ['start_date', 'final_date'])) {
            return Order::whereBetween('created_at', [
                [$data['start_date'], $data['final_date']]
            ]);
        }

        if (Helper::filled($data, 'start_date')) {
            return Order::whereDate('created_at', $data['start_date']);
        }

        return Order::query();
    }

    public function getOrdersClosedQuery($ordersQuery)
    {
        return $ordersQuery->whereNotNull('closed_at');
    }

    public function getOrdersWithNoPaymentsQuery($ordersQuery)
    {
        return $ordersQuery->has('payments', '=', 0);
    }

    public function validator($data)
    {
        return Validator::make($data, [
            'start_date' => ['sometimes', 'required', 'date'],
            'final_date' => ['nullable', 'date', 'after:start_date']
        ]);
    }

    public function getFormattedData($data)
    {
        return (new Formatter($data))
            ->date(['start_date', 'final_date'])
            ->get();
    }
}
