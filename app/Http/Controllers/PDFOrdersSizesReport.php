<?php

namespace App\Http\Controllers;

use App\GraphQL\Queries\OrdersSizesReport;
use App\Models\GarmentMatch;
use App\Models\GarmentSize;
use App\Models\Material;
use App\Models\Model;
use App\Models\NeckType;
use App\Models\SleeveType;
use App\Util\Mask;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class PDFOrdersSizesReport extends PDFController
{
    protected static $TYPES_OF_GARMENTS = [
        'model' => Model::class,
        'material' => Material::class,
        'neck_type' => NeckType::class,
        'sleeve_type' => SleeveType::class
    ];

    protected static $INCLUDED_IN_MAP = [
        'model' => ['abbr' => 'MO'],
        'material' => ['abbr' => 'MA'],
        'neck_type' => ['abbr' => 'TG'],
        'sleeve_type' => ['abbr' => 'TM']
    ];

    public function __invoke(Request $request)
    {
        OrdersSizesReport::validator($request->all())->validate();

        $displayIndicators = $request->get('indicators');
        $types = $this->getTypes($request->only('groups'));
        $dates = $request->only(['initial_date', 'final_date']);
        $ordersSizes = $this->getOrdersSizes($dates, $types, $displayIndicators);

        $pdf = PDF::loadView(
            'pdf.orders-sizes.index',
            [
                'types' => $types,
                'ordersSizes' => $ordersSizes,
                'title' => 'Relatório de tamanhos',
                'subtitle' => $this->getSubtitle($dates),
                'indicators' => $displayIndicators,
                'includedInMap' => static::$INCLUDED_IN_MAP,
                'isColumnEmpty' => fn ($size, $metadata) => $size['quantity'] === 0
                    && !in_array($size['id'], Arr::pluck($metadata['sizes'], 'id')),
                'getPresentInText' => function ($order, $currentIndex, $currentType) use ($types) {
                    $indexes = collect($order['present_in']);
                    $grouped = [];

                    foreach ($types as $type => $model) {
                        $abbr = static::$INCLUDED_IN_MAP[$type]['abbr'];
                        $group = $indexes->filter(fn ($item) => Str::contains($item, $type));

                        $indexesPresent = $group->map(fn ($item) => Arr::last(explode(':', $item)) + 1);

                        if ($currentType === $type) {
                            $indexesPresent = $indexesPresent->filter(fn ($index) => $index !== $currentIndex + 1);
                        }

                        if (count($indexesPresent)) {
                            $grouped[] = $abbr . ': ' . $indexesPresent->implode(', ');
                        }
                    }

                    if ($indexesPresent->isEmpty()) {
                        return '';
                    }

                    return implode(' | ', $grouped);
                }
            ]
        );

        return $pdf->stream('relatorio-por-tamanhos');
    }

    public function getTypes($types)
    {
        return array_filter(
            static::$TYPES_OF_GARMENTS,
            fn ($_, $key) => in_array($key, $types['groups']),
            ARRAY_FILTER_USE_BOTH
        );
    }

    public function getOrdersSizes($dates, $types, $displayIndicators)
    {
        $query = GarmentMatch::join('garments', 'garment_matches.id', '=', 'garments.garment_match_id')
            ->join('garment_garment_size', 'garments.id', '=', 'garment_garment_size.garment_id')
            ->join('garment_sizes', 'garment_garment_size.garment_size_id', '=', 'garment_sizes.id')
            ->join('orders', 'garments.order_id', '=', 'orders.id')
            ->join('order_status', 'orders.id', '=', 'order_status.order_id')
            ->whereNotIn('order_status.status_id', [17, 5, 18, 8, 10, 9, 21])
            ->whereIn('order_status.status_id', [24, 22, 23, 11, 15]);

        $this->queryDates($query, $dates, $types);
        $this->querySelect($query, $types);

        $orders = $query->get();

        if ($orders->isEmpty()) {
            return [];
        }

        $groupedOrders = $this->getGroupedOrders($orders, $types);
        $groupedOrders = $this->sortOrders($groupedOrders, $types);
        $groupedOrders = $this->sortGroupsByMetadataOrder($groupedOrders, $types);

        if ($displayIndicators) {
            $groupedOrders = $this->addIndicatorsToOrders($groupedOrders, $types);
        }

        $groupedOrders = $this->reindexAssocArray($groupedOrders);

        return $groupedOrders;
    }

    public function reindexAssocArray($groupedOrders)
    {
        return array_map(fn ($group) => array_values($group), $groupedOrders);
    }

    public function sortOrders($groupedOrders, $types)
    {
        foreach ($types as $type => $model) {
            foreach ($groupedOrders[$type] as $key => $orders) {
                $groupedOrders[$type][$key] = $orders->sortByDesc('created_at');
            }
        }

        return $groupedOrders;
    }

    public function addIndicatorsToOrders($groupedOrders, $types)
    {
        $groupedOrders = array_map(fn ($item) => array_values($item), $groupedOrders);
        $groupedIds = [];

        foreach ($types as $type => $model) {
            foreach ($groupedOrders[$type] as $key => $orders) {
                $groupedIds[$type][$key] = Arr::pluck($orders, 'id');
            }
        }

        foreach ($types as $type => $model) {
            foreach ($groupedOrders[$type] as $groupKey => $orders) {
                foreach ($orders as $key => $order) {
                    $groupedOrders[$type][$groupKey][$key] = array_merge($order, [
                        'present_in' => $this->indexesWhereOrderIsPresent($groupedIds, $types, $order)
                    ]);
                }
            }
        }

        return $groupedOrders;
    }

    public function indexesWhereOrderIsPresent($groupedIds, $types, $order)
    {
        $indexes = [];

        foreach ($types as $type => $model) {
            foreach ($groupedIds[$type] as $key => $ids) {
                if (in_array($order['id'], $ids)) {
                    $indexes[] = "$type:$key";
                }
            }
        }

        return $indexes;
    }

    public function querySelect($ordersQuery, $types)
    {
        $typesToInclude = array_map(
            fn ($_, $type) => "garment_matches.{$type}_id",
            $types,
            array_keys($types)
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

    public function queryDates($ordersQuery, $dates)
    {
        if (isset($dates['final_date'])) {
            $ordersQuery->whereBetween('orders.created_at', [
                $dates['initial_date'],
                $dates['final_date'] . ' ' . '23:59:59'
            ]);

            return;
        }

        $ordersQuery->whereDate('orders.created_at', $dates['initial_date']);
    }

    public function getGroupedOrders($orders, $types)
    {
        $group = [];

        foreach ($types as $type => $model) {
            $group[$type] = $this->getFormattedOrders($orders, "{$type}_id");
            $group["{$type}_metadata"] = $this->getMetadata($group[$type], $type);
        }

        return $group;
    }

    public function sortGroupsByMetadataOrder($groupedOrders, $types)
    {
        $groups = [];

        foreach ($types as $type => $model) {
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

        return $metadata;
    }

    public function getSubtitle($dates)
    {
        $formattedDates = array_map(fn ($date) => Mask::date($date), $dates);

        if (isset($formattedDates['final_date'])) {
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
        ])->sortBy('order')->values();
    }
}
