<?php

namespace App\GraphQL\Builders;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CashFlowEntriesBuilder
{
    /**
     * Faz um join nas tabelas "payments" e "expenses"
     * para exibir o fluxo de caixa.
     *
     * O campo "description" é usado para exibir
     * o código do pedido do pagamento,
     * bem como a descrição da despesa
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function __invoke()
    {
        $payments = self::paymentsQuery();
        $expenses = self::expensesQuery();

        $merged = $payments->unionAll($expenses);

        return DB::table(
            DB::raw("({$merged->toSql()}) AS merged")
        )->mergeBindings($merged);
    }

    public static function paymentsQuery()
    {
        return DB::table('payments')
            ->join('orders', 'payments.order_id', '=', 'orders.id')
            ->where('is_confirmed', '=', true)
            ->select([
                'payments.id',
                DB::raw('null AS user_id'),
                'order_id',
                'payment_via_id AS via_id',
                DB::raw('null AS type_id'),
                'value',
                'date',
                'orders.code AS description',
                'note',
                DB::raw('null AS employee_name'),
                DB::raw('null AS receipt_path'),
                'confirmed_at',
                'payments.created_at',
                'payments.updated_at'
            ]);
    }

    public static function expensesQuery()
    {
        return DB::table('expenses')->select([
            'id',
            'user_id',
            DB::raw('null AS order_id'),
            'expense_via_id AS via_id',
            'expense_type_id AS type_id',
            'value',
            'date',
            'description',
            DB::raw('null AS note'),
            'employee_name',
            'receipt_path',
            DB::raw('null AS confirmed_at'),
            'created_at',
            'updated_at'
        ]);
    }
}
