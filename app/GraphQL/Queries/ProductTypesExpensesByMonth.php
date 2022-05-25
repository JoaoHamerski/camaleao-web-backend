<?php

namespace App\GraphQL\Queries;

use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProductTypesExpensesByMonth
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $date = Carbon::now();

        $expenses = DB::table('product_types')
            ->join('expenses', 'product_types.id', '=', 'expenses.product_type_id')
            ->whereBetween('expenses.date', [
                $date->clone()->startOf('month')->toDateString(),
                $date->clone()->endOf('month')->toDateString()
            ])
            ->whereNotNull('expenses.is_confirmed')
            ->groupBy('expenses.product_type_id')
            ->get([
                'name',
                DB::raw('SUM(value) as expense')
            ]);

        return $expenses;

        return $expenses->map(fn ($expense) => [
            'name' => $expense->name,
            'expense' => number_format($expense->expense, 2, '.', '')
        ]);
    }
}
