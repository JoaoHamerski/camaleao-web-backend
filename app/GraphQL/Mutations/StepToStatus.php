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
        $order = null;
        $sector = null;
        $status = null;

        Validator::make($args, [
            'order_id' => ['required', 'exists:orders,id'],
        ])->validate();

        $order = Order::find($args['order_id']);
        $sector = $order->getSectorWithRematchedStatus();

        Validator::make($args, [
            'status_id' => [
                'required',
                'exists:status,id',
                Rule::in($sector->status->pluck('id')->toArray())
            ]
        ])->validate();

        $status = Status::find($args['status_id']);

        $this->attachStatusToOrder($order, $status);

        return $order->fresh();
    }

    public function attachStatusToOrder(Order $order, Status $status)
    {
        $order->concludedStatus()
            ->syncWithPivotValues($status, ['user_id' => Auth::id()], false);

        $order->update(['status_id' => $status->id]);

        $order->syncStatus();
    }
}
