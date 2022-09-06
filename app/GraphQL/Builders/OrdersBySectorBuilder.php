<?php

namespace App\GraphQL\Builders;

use App\Models\Order;
use Illuminate\Support\Facades\Validator;

class OrdersBySectorBuilder
{
    public function __invoke($root, array $args)
    {
        Validator::make($args, [
            'sector_id' => ['required', 'exists:sectors,id']
        ])->validate();

        return Order::getBySector($args['sector_id']);
    }
}
