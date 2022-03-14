<?php

namespace App\GraphQL\Mutations;

use App\Models\Order;
use Illuminate\Support\Facades\Validator;

class NoteCreate
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'order_id' => ['required', 'exists:orders,id'],
            'text' => ['required']
        ], $this->errorMessages())->validate();

        $order = Order::find($args['order_id']);
        $note = $order->notes()->create($args);

        return $note;
    }

    public function errorMessages()
    {
        return [
            'text.required' => __('general.validation.text_required')
        ];
    }
}
