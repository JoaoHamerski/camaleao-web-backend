<?php

namespace App\Http\Controllers;

use App\Models\GarmentMatch;
use App\Models\GarmentSize;
use App\Models\Model;
use App\Models\NeckType;
use App\Util\Mask;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Arr;

class PDFOrdersSizesReport extends Controller
{
    protected static $TYPES_OF_GARMENTS = [
        'model' => Model::class,
        'neck_type' => NeckType::class
    ];

    public function __invoke(Request $request)
    {
        $types = static::$TYPES_OF_GARMENTS;
        $dates = $request->only(['initial_date', 'final_date']);
        $ordersSizes = $this->getOrdersSizes($dates);

        $pdf = PDF::loadView(
            'pdf.orders-sizes.index',
            [
                'types' => $types,
                'ordersSizes' => $ordersSizes,
                'title' => 'Relatório de tamanhos',
                'subtitle' => $this->getSubtitle($dates),
                'isColumnEmpty' => fn ($size, $metadata) => $size['quantity'] === 0
                    && !in_array($size['id'], Arr::pluck($metadata['sizes'], 'id')),
            ]
        );

        return $pdf->stream('relatorio-por-tamanhos');
    }

    public function getOrdersSizes($data)
    {
        $query = GarmentMatch::join('garments', 'garment_matches.id', '=', 'garments.garment_match_id')
            ->join('garment_garment_size', 'garments.id', '=', 'garment_garment_size.garment_id')
            ->join('garment_sizes', 'garment_garment_size.garment_size_id', '=', 'garment_sizes.id')
            ->join('orders', 'garments.order_id', '=', 'orders.id');

        $this->queryDates($query, $data);
        $this->querySelect($query);

        $orders = $query->get();

        $groupedOrders = $this->getGroupedOrders($orders);
        $groupedOrders = $this->sortGroupsByMetadataOrder($groupedOrders);

        return $groupedOrders;
    }

    public function querySelect($ordersQuery)
    {
        $typesToInclude = array_map(
            fn ($_, $type) => "garment_matches.{$type}_id",
            static::$TYPES_OF_GARMENTS,
            array_keys(static::$TYPES_OF_GARMENTS)
        );

        $ordersQuery->select(array_merge([
            'orders.id',
            'orders.code',
            'orders.created_at',
            'orders.client_id',
            'garment_garment_size.garment_size_id AS size_id',
            'garment_garment_size.quantity AS size_quantity',
            'garment_sizes.name AS size_name',
            'garment_sizes.order AS size_order'
        ], $typesToInclude));
    }

    public function queryDates($ordersQuery, $data)
    {
        if (isset($data['final_date'])) {
            $ordersQuery->whereBetween('orders.created_at', [
                $data['initial_date'],
                $data['final_date'] . ' ' . '23:59:59'
            ]);

            return;
        }

        $ordersQuery->whereDate('orders.created_at', $data['initial_date']);
    }

    public function getGroupedOrders($orders)
    {
        $group = [];

        foreach (static::$TYPES_OF_GARMENTS as $type => $model) {
            $group[$type] = $this->getFormattedOrders($orders, "{$type}_id");
            $group["{$type}_metadata"] = $this->getMetadata($group[$type], $type);
        }

        return $group;
    }

    public function sortGroupsByMetadataOrder($groupedOrders)
    {
        $groups = [];

        foreach (static::$TYPES_OF_GARMENTS as $type => $model) {
            foreach ($groupedOrders["{$type}_metadata"] as $key => $metadata) {
                $groups[$type][$key] = $groupedOrders[$type][$key];
            }

            $groups["{$type}_metadata"] = $groupedOrders["{$type}_metadata"];
        }

        return $groups;
    }

    public function getFormattedOrders($orders, $groupedBy)
    {
        $groupedOrders = $orders->groupBy($groupedBy);

        return $groupedOrders->map(function ($orders) {
            $grouped = $orders->groupBy('id');

            return $grouped->map(function ($orders) {
                $order = $orders[0];

                return [
                    'id' => $order->id,
                    'code' => $order->code,
                    'url' => $this->getOrderUrl($order),
                    'created_at' => $order->created_at,
                    'sizes' => $this->getOrderSizes($orders),
                ];
            })->values();
        });
    }

    public function getMetadata($groupedOrders, $type)
    {
        $metadata = $groupedOrders->map(function ($orders, $key) use ($type) {
            $model = static::$TYPES_OF_GARMENTS[$type]::find($key);
            $sizes = $this->getSizesOfMaxLength($orders);

            return [
                'id' => data_get($model, 'id'),
                'order' => data_get($model, 'order'),
                'name' => data_get($model, 'name'),
                'sizes' => $sizes,
                'total' => array_sum(Arr::pluck($sizes, 'quantity'))
            ];
        });

        $metadata = $metadata->sortBy('order')->toArray();

        foreach ($metadata as $key => $item) {
            data_set(
                $metadata,
                "$key.sizes",
                $this->removeEmptySizesFromMetadata($item['sizes'])
            );
        }

        return collect($metadata);
    }

    public function getSubtitle($dates)
    {
        $formattedDates = array_map(fn ($date) => Mask::date($date), $dates);

        if ($formattedDates['final_date']) {
            return implode(' - ', $formattedDates);
        }

        return $formattedDates['initial_date'];
    }

    public function getOrderUrl($order)
    {
        $frontendUrl = config('app.frontend_url');

        return "{$frontendUrl}/clientes/$order->client_id/pedidos/$order->id";
    }

    public function removeEmptySizesFromMetadata($sizes)
    {
        return array_values(
            array_filter($sizes, fn ($size) => $size['quantity'] > 0)
        );
    }

    public function getSizesOfMaxLength($orders)
    {
        $size = 0;
        $sizes = [];

        $orders->each(function ($order) use (&$size, &$sizes) {
            if ($order['sizes']->count() > $size) {
                $size = $order['sizes']->count();
                $sizes = $order['sizes']->toArray();
            }
        });

        foreach ($sizes as $key => $size) {
            $sizes[$key]['quantity'] = $orders
                ->pluck('sizes')
                ->collapse()
                ->where('id', $size['id'])
                ->sum('quantity');
        }

        return $sizes;
    }

    public function getOrderSizes($order)
    {

        $orderSizes = $order->unique('size_id');
        $sizes = GarmentSize::all();

        return $sizes->map(fn ($size) => [
            'id' => $size->id,
            'order' => $size->order,
            'name' => $size->name,
            'quantity' => $orderSizes->where('size_id', $size->id)->sum('size_quantity'),
        ])->sortBy('order')
            ->values();
    }
}
