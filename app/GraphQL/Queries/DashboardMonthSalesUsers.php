<?php

namespace App\GraphQL\Queries;

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
        $results = User::select(['users.*', DB::raw('SUM(orders.price) as total')])
            ->leftJoin('orders', function ($query) {
                $query->on('users.id', '=', 'orders.user_id')
                    ->whereBetween('orders.created_at', [
                        Carbon::now()->startOfMonth(),
                        Carbon::now()->endOfMonth()
                    ]);
            })
            ->orderBy('total', 'desc')
            ->groupBy('users.id')
            ->get();

        $total = $results->sum('total');

        $results = $results->map(
            fn ($user) => [
                'user' => $user,
                'total' => $user['total'] ? number_format($user['total'], 2, '.', '') : 0,
                'percentage' => $this->getPercentage($total, $user['total'])
            ]
        );

        $results = $results->filter(fn ($result) => $result['user']['id'] !== 3);

        if (!Auth::user()->hasRole('GERENCIA')) {
            return $results->filter(
                fn ($result) => $result['user']['id'] === Auth::id()
            );
        }

        return $results;
    }

    public function getPercentage($total, $userTotal)
    {
        if ($total === 0) {
            return 0;
        }

        return number_format(
            ($userTotal * 100) / $total,
            2,
            '.',
            ''
        );
    }
}
