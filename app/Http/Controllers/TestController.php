<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Payment;
use App\Util\Formatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\Exception\NoSuchIndexException;

class TestController extends Controller
{
    public function test()
    {
        $payments = DB::table('payments')
            ->join('orders', 'payments.order_id', '=', 'orders.id')
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
                'payments.created_at',
                'payments.updated_at'
            ]);

        $expenses = DB::table('expenses')->select([
            'id',
            'user_id',
            DB::raw('null AS order_id'),
            'expense_via_id AS via_id',
            'expense_type_id AS type_id',
            'value',
            'date',
            'description',
            DB::raw('null AS note'),
            'created_at',
            'updated_at'
        ]);

        $merged = $payments->unionAll($expenses)->orderBy('created_at', 'desc');
        $result = DB::table(
            DB::raw("({$merged->toSql()}) AS merged")
        )->mergeBindings(($merged));



        dd($result);
    }
}
