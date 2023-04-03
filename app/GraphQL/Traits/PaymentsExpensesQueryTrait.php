<?php

namespace App\GraphQL\Traits;

use App\Util\Helper;
use Illuminate\Support\Facades\DB;

trait PaymentsExpensesQueryTrait
{
    public function paymentsQuery()
    {
        return DB::table('payments')
            ->leftJoin('orders', 'payments.order_id', '=', 'orders.id')
            ->whereNotNull('payments.date')
            ->select([
                'payments.id',
                'bank_uid',
                DB::raw('null AS user_id'),
                'payments.order_id',
                'payment_via_id AS via_id',
                DB::raw('null AS type_id'),
                DB::raw('null AS product_type_id'),
                DB::raw('null AS employee_id'),
                'sponsorship_client_id',
                'value',
                'date',
                'orders.code AS description',
                'note',
                DB::raw('null AS employee_name'),
                DB::raw('null AS receipt_path'),
                'payments.confirmed_at',
                'payments.is_confirmed',
                'payments.created_at',
                'payments.updated_at'
            ]);
    }

    public function expensesQuery()
    {
        return DB::table('expenses')->select([
            'id',
            'bank_uid',
            'user_id',
            DB::raw("-1 AS order_id"),
            'expense_via_id AS via_id',
            'expense_type_id AS type_id',
            'product_type_id',
            'employee_id',
            DB::raw('null AS sponsorship_client_id'),
            'value',
            'date',
            'description',
            DB::raw('null AS note'),
            'employee_name',
            'receipt_path',
            'confirmed_at',
            'is_confirmed',
            'created_at',
            'updated_at'
        ]);
    }
}
