<?php

namespace App\GraphQL\Queries;

use App\Models\Expense;
use App\Util\Formatter;
use Illuminate\Support\Facades\Validator;
use App\Models\Payment;
use App\Util\Helper;

class CashFlowBalance
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $data = $this->getFormattedData($args);
        $this->validator($data)->validate();

        $balance = $this->evaluateBalance($data);

        return $balance;
    }

    public static function expensesQuery($data)
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

    public static function paymentsQuery($data)
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
        $totalExpensesValue = $this->expensesQuery($data)
            ->where('is_confirmed', true)
            ->sum('value');

        $totalPaymentsValue = $this->paymentsQuery($data)
            ->where('is_confirmed', true)
            ->sum('value');

        return bcsub($totalPaymentsValue, $totalExpensesValue, 2);
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
