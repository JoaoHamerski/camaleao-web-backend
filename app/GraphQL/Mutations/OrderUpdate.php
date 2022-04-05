<?php

namespace App\GraphQL\Mutations;

use App\Models\Order;
use App\GraphQL\Traits\OrderTrait;

class OrderUpdate
{
    use OrderTrait;

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $order = Order::find($args['id']);
        $data = $this->getFormattedData($args, $order);

        $this->validator($data, $order)->validate();

        $data = $this->handleFilesUpload($data, $order);

        $order->fill($data);

        if (isset($data['clothing_types'])) {
            $order->clothingTypes()->sync(
                $this->getFilledClothingTypes($data)
            );

            if (!$order->isPreRegistered()) {
                $this->handleCommissions(
                    $order->fresh(),
                    $isUpdate = true
                );
            }
        }

        $order->save();

        return $order;
    }
}
