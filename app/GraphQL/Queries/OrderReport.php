<?php

namespace App\GraphQL\Queries;

use App\Models\Order;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class OrderReport
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'id' => ['required', 'exists:orders,id']
        ])->validate();

        $order = Order::find($args['id']);

        return URL::temporarySignedRoute(
            'pdf.order-report',
            now()->addSeconds(60),
            compact('order')
        );
    }
}
