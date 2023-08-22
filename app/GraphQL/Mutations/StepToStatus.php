<?php

namespace App\GraphQL\Mutations;

use App\Models\Order;
use App\Models\Status;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class StepToStatus
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'order_id' => ['required', 'exists:orders,id'],
            'status_id' => ['required', 'exists:status,id'],
            'override_option' => ['nullable', Rule::in(['update', 'keep'])]
        ])->validate();

        (new OrderUpdateStatus)->__invoke(null, [
            'id' => $args['order_id'],
            'status_id' => $args['status_id'],
            'override_option' => $args['override_option']
        ]);

        return Order::find($args['order_id']);
    }
}
