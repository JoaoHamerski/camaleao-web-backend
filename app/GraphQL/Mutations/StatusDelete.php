<?php

namespace App\GraphQL\Mutations;

use App\Models\Order;
use App\Models\Status;
use App\Models\OrderStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StatusDelete
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'id' => ['required', 'exists:status,id'],
            'replace_status_id' => ['required', 'exists:status,id'],
            'password' => ['required', 'current_password']
        ], $this->errorMessages())->validate();

        $statusToDelete = Status::find($args['id']);
        $statusToReplace = Status::find($args['replace_status_id']);

        activity()->withoutLogs(function () use ($statusToDelete, $statusToReplace) {
            $query = Order::where('status_id', $statusToDelete->id)
                ->whereNull('closed_at');

            $orders = $query->get();

            $query->update(['status_id' => $statusToReplace->id]);

            $this->syncAllOrdersStatus($orders);
        });

        $statusToDelete->delete();

        return $statusToDelete;
    }

    public function syncAllOrdersStatus($orders)
    {
        $orders->each(function ($order) {
            $order->refresh();
            $order->syncStatus();
        });
    }

    private function errorMessages()
    {
        return [
            'password.required' => __('validation.rules.required'),
            'password.current_password' => __('validation.rules.current_password'),
            'replace_status_id.required' => __('validation.rules.required_list', [
                'pronoun' => 'um',
                'attribute' => 'status para substituir'
            ])
        ];
    }
}
