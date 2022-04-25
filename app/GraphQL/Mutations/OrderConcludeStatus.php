<?php

namespace App\GraphQL\Mutations;

use App\Models\Order;
use App\Models\AppConfig;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class OrderConcludeStatus
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'id' => ['required', 'exists:orders,id'],
            'field' => ['required', Rule::in([
                'print_date',
                'seam_date',
                'delivery_date'
            ])]
        ])->validate();

        $order = Order::find($args['id']);

        $updateTo = $this->getStatusIdToUpdate($order);

        if ($updateTo) {
            $order->update(['status_id' => $updateTo]);
        }

        return $order
            ->refresh()
            ->isConcluded(null, $args['field'])
            ->canBeConcluded(null, $args['field']);
    }

    private function getStatusIdToUpdate($order)
    {
        $collection = collect(AppConfig::get('status', 'update_status_map'));

        $index = $collection->search(
            fn ($item) => in_array(
                $order->status->id,
                $item['status_is']
            )
        );

        if ($index === false) {
            return false;
        }

        $status = $collection->get($index);

        return $status['update_to'];
    }
}
