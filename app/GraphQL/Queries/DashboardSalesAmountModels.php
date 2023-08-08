<?php

namespace App\GraphQL\Queries;

use App\Models\Model;
use Carbon\Carbon;

final class DashboardSalesAmountModels
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        $query = $this->buildQuery($args['date']);

        return $this->getFormattedData($query);
    }

    public function getFormattedData($query)
    {
        $result = $query->get();
        $result = $result->groupBy('model_id');
        $totalAmount = $result->reduce(
            fn ($total, $item) => bcadd($total, $item->sum('price'), 2),
            0
        );

        return $result->map(function ($item, $index) use ($totalAmount) {
            $itemAmount = $item->sum('price');

            return [
                'model' => Model::find($index),
                'amount' => number_format($itemAmount, 2, '.', ''),
                'shirts_count' => $item->sum('quantity'),
                'sales_percentage' => $this->getSalesPercentage($totalAmount, $itemAmount)
            ];
        })->sortByDesc('amount');
    }

    public function getSalesPercentage($totalAmount, $itemAmount)
    {
        return bcdiv(
            $itemAmount * 100,
            $totalAmount,
            2
        );
    }

    public function queryDate($query, $date)
    {
        if ($date === 'month') {
            $query->whereBetween('orders.created_at', [
                Carbon::now()->startOfMonth()->toDateString(),
                Carbon::now()->endOfMonth()->toDateTimeString()
            ]);
        }

        if ($date === 'year') {
            $query->whereBetween('orders.created_at', [
                Carbon::now()->startOfYear()->toDateString(),
                Carbon::now()->endOfYear()->toDateTimeString()
            ]);
        }
    }

    public function buildQuery($date)
    {
        $query = Model::join('garment_matches', 'model_id', '=', 'models.id')
            ->join('garments', 'garment_matches.id', '=', 'garments.garment_match_id')
            ->join('orders', 'garments.order_id', '=', 'orders.id')
            ->groupBy(['orders.id', 'models.id'])
            ->select(
                'orders.id AS order_id',
                'models.id AS model_id',
                'orders.quantity',
                'orders.price'
            );

        $this->queryDate($query, $date);

        return $query;
    }
}
