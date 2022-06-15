<?php

namespace App\GraphQL\Mutations;

use App\Models\Order;
use Illuminate\Support\Facades\Validator;

class WeeklyCalendarReorder
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $data = $args['input'];
        $this->validator($data)->validate();

        activity()->withoutLogs(function () use ($data) {
            foreach ($data as $order) {
                Order::find($order['id'])->update(['order' => $order['order']]);
            }
        });

        return Order::whereIn('id', collect($data)->pluck('id'))
            ->orderBy('order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function validator($data)
    {
        return Validator::make($data, [
            'id.*' => ['required', 'exists:orders,id'],
            'order.*' => ['required', 'numeric']
        ]);
    }
}
