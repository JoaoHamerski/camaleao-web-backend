<?php

namespace App\Http\Controllers;

use App\Models\GarmentMatch;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;

class PDFOrdersSizesReport extends Controller
{
    public function __invoke(Request $request)
    {
        $groupedOrders = $this->getOrdersSizes(
            $request->only(['initial_date', 'final_date'])
        );
        $metadata = $this->getMetadata($groupedOrders);

        $pdf = PDF::loadView(
            'pdf.orders-sizes.index',
            [
                'groupedOrders' => $groupedOrders,
                'metadata' => $metadata,
                'title' => 'Algo'
            ]
        );

        return $pdf->stream('relatorio-por-tamanhos');
    }

    public function getOrdersSizes($data)
    {
        $orders = GarmentMatch::join('garments', 'garment_matches.id', '=', 'garments.garment_match_id')
            ->join('garment_garment_size', 'garments.id', '=', 'garment_garment_size.garment_id')
            ->join('garment_sizes', 'garment_garment_size.garment_size_id', '=', 'garment_sizes.id')
            ->join('orders', 'garments.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$data['initial_date'], $data['final_date']])
            ->select([
                'orders.id',
                'orders.code',
                'orders.created_at',
                'orders.client_id',
                'garment_matches.model_id',
                'garment_garment_size.garment_size_id AS size_id',
                'garment_garment_size.quantity AS size_quantity',
                'garment_sizes.name AS size_name',
                'garment_sizes.order AS size_order'
            ])
            ->get();

        $groupedOrders = $orders->groupBy('model_id');

        $groupedOrders = $this->mappedOrders($groupedOrders);

        return $groupedOrders;
    }

    public function mappedOrders($groupedOrders)
    {
        return $groupedOrders->map(function ($orders) {
            $grouped = $orders->groupBy('id');

            return $grouped->map(function ($orders) {
                $order = $orders[0];

                return [
                    'id' => $order->id,
                    'model_id' => $order->model_id,
                    'code' => $order->code,
                    'client_id' => $order->client_id,
                    'created_at' => $order->created_at,
                    'sizes' => $this->getOrderSizes($orders),
                ];
            })->values();
        });
    }

    public function getMetadata($groupedOrders)
    {
        return $groupedOrders->map(fn ($orders) => [
            'sizes' => $this->getSizesOfMaxLength($orders)
        ]);
    }

    public function getSizesOfMaxLength($orders)
    {
        $size = 0;
        $sizes = [];

        $orders->each(function ($order) use (&$size, &$sizes) {
            if ($order['sizes']->count() > $size) {
                $size = $order['sizes']->count();
                $sizes =  $order['sizes'];
            }
        });

        return $sizes;
    }

    public function getOrderSizes($order)
    {
        $sizes = $order->unique('size_id');

        return $sizes->map(fn ($size) => [
            'id' => $size->size_id,
            'order' => $size->size_order,
            'name' => $size->size_name,
            'quantity' => $sizes->where('size_id', $size->size_id)->sum('size_quantity'),
        ])->sortBy('order')->values();
    }
}
