<?php

namespace App\GraphQL\Queries;

use App\Models\GarmentMatch;
use App\Models\Order;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

final class GarmentMatches
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'order_id' => ['sometimes', 'required', 'exists:orders,id']
        ])->validate();

        $matchesToInclude = collect([]);

        if (isset($args['order_id'])) {
            $matchesToInclude = $this->getMatchesToInclude($args);
        }

        $matches = GarmentMatch::all();

        return $matches->merge($matchesToInclude);
    }

    public function getMatchesToInclude($args)
    {
        $order = Order::find($args['order_id']);
        $matches = GarmentMatch::whereIn(
            'id',
            $order->garments->pluck('garment_match_id')
        )->withTrashed()->get();

        return $matches;
    }
}
