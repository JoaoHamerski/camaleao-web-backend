<?php

namespace App\GraphQL\Queries;

use App\Models\Model;
use Illuminate\Support\Facades\DB;

final class DashboardSalesAmountModels
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        $query = $this->buildQuery();
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

    public function buildQuery()
    {
        return Model::join('garment_matches', 'model_id', '=', 'models.id')
            ->join('garments', 'garment_matches.id', '=', 'garments.garment_match_id')
            ->join('orders', 'garments.order_id', '=', 'orders.id')
            ->groupBy(['orders.id', 'models.id'])
            ->select(
                'orders.id AS order_id',
                'models.id AS model_id',
                'orders.quantity',
                'orders.price'
            );
    }
}
