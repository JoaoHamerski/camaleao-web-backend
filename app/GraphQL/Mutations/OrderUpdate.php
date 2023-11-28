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
        $data = $this->evaluateOrderAttributes($data, $order);

        $this->validator($data, $order)->validate();

        $data = $this->handleFilesUpload($data, $order);

        $order->fill($data);

        if (isset($data['clothing_types'])) {
            $order->clothingTypes()->sync(
                $this->getFilledClothingTypes($data)
            );
        }

        if ($order->isDirty('print_date')) {
            $order->order = null;
        }

        $order->save();

        if ($order->client->clientRecommended) {
            $this->updateClientBonus($order);
        }

        if (!isset($data['clothing_types'])) {
            $this->syncItems($data, $order, true);
        }

        return $order;
    }

    public function updateClientBonus($order)
    {
        $order = $order->fresh();

        if (!$order->bonus) {
            return;
        }

        $oldBonus = $order->bonus->value;


        $newBonus = bcmul(
            bcsub($order->price, $order->shipping_value ?? 0, 2),
            bcdiv($order->recommendation_bonus_percent, 100, 2),
            2
        );

        $order->bonus->update([
            'value' => $newBonus
        ]);

        $order->bonus->client->update([
            'bonus' => bcadd(
                bcsub($order->bonus->client->bonus, $oldBonus, 2),
                $newBonus,
                2
            )
        ]);
    }
}
