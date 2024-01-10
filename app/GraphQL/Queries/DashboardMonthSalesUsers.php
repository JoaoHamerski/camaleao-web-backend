<?php

namespace App\GraphQL\Queries;

use App\Models\OrderProduct;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

final class DashboardMonthSalesUsers
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        $results = $this->productsQuery()->get();

        $results = $results->map(
            fn ($user) => [
                'user' => $user,
                'products' => [
                    'total' => $this->getTotal($user['products_total']),
                    'percentage' => $this->getPercentage(
                        $results->sum('products_total'),
                        $user['products_total']
                    )
                ],
                'direct_cost_items' => [
                    'total' => $this->getTotal($user['direct_cost_total']),
                    'percentage' => $this->getPercentage(
                        $results->sum('direct_cost_total'),
                        $user['direct_cost_total']
                    )
                ],
            ]
        );

        // $results = $this->hideMyselfFromRecords($results);

        if (!Auth::user()->hasRole('GERENCIA')) {
            return $results->filter(
                fn ($result) => $result['user']['id'] === Auth::id()
            );
        }

        return $results;
    }

    public function hideMyselfFromRecords($results)
    {
        return $results->filter(
            fn ($result) => $result['user']['id'] !== 3
        );
    }

    public function productsQuery()
    {
        $subQueryProducts = OrderProduct::where('value', '>', '0')->select('*');
        $subQueryDirectCost = OrderProduct::where('value', '<', '0')->select('*');

        return User::select([
            'users.*',
            DB::raw('SUM(sub_products.value * sub_products.quantity) AS products_total'),
            DB::raw('SUM(sub_direct_cost.value * sub_direct_cost.quantity) AS direct_cost_total')
        ])->leftJoin('orders', function ($query) {
            $query->on('users.id', '=', 'orders.user_id')
                ->whereBetween('orders.created_at', [
                    Carbon::now()->startOfMonth(),
                    Carbon::now()->endOfMonth()
                ]);
        })
            ->leftJoinSub($subQueryProducts, 'sub_products', function ($join) {
                $join->on('orders.id', '=', 'sub_products.order_id')
                    ->whereBetween('orders.created_at', [
                        Carbon::now()->startOfMonth(),
                        Carbon::now()->endOfMonth()
                    ]);
            })
            ->leftJoinSub($subQueryDirectCost, 'sub_direct_cost', function ($join) {
                $join->on('orders.id', '=', 'sub_direct_cost.order_id')
                    ->whereBetween('orders.created_at', [
                        Carbon::now()->startOfMonth(),
                        Carbon::now()->endOfMonth()
                    ]);
            })
            ->groupBy('users.id')
            ->orderBy('products_total', 'desc');
    }


    public function getTotal($total)
    {
        if (!$total) {
            return 0;
        }

        return number_format(abs($total), 2, '.', '');
    }

    public function getPercentage($total, $userTotal)
    {
        if ($total === 0) {
            return 0;
        }

        return number_format(
            (abs($userTotal) * 100) / abs($total),
            2,
            '.',
            ''
        );
    }
}
