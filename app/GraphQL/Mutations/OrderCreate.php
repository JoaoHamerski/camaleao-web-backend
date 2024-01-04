<?php

namespace App\GraphQL\Mutations;

use Carbon\Carbon;
use App\Models\Client;
use App\GraphQL\Traits\OrderTrait;
use App\Models\Bonus;

class OrderCreate
{
    use OrderTrait;

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $client = Client::find($args['client_id']);
        $data = $this->getFormattedData($args);
        $data = $this->evaluateOrderAttributes($data);

        $this->validator($data)->validate();

        $data = $this->handleFilesUpload($data);

        $order = $client->orders()->create($data);

        $this->registerProducts($data, $order);

        if ($client->clientRecommended) {
            $this->addBonusToRecommendedClient($client, $order);
        }

        return $order;
    }

    public function addBonusToRecommendedClient($client, $order)
    {
        $bonus = bcmul(
            bcsub($order->price, $order->shipping_value ?? 0, 2),
            bcdiv($order->recommendation_bonus_percent, 100, 2),
            2
        );

        $client->clientRecommended->increaseBonus($bonus);

        Bonus::create([
            'client_id' => $client->clientRecommended->id,
            'order_id' => $order->id,
            'value' => $bonus
        ]);
    }


    public function hasDownPayment($data)
    {
        return !empty($data['down_payment'])
            && !empty($data['payment_via_id']);
    }

    public function createDownPayment($order, $data)
    {
        return (new PaymentCreate)->__invoke(null, [
            'order_id' => $order->id,
            'payment_via_id' => $data['payment_via_id'],
            'value' => $data['down_payment'],
            'date' => Carbon::now()->format('d/m/Y'),
            'note'  => 'Pagamento de entrada',
            'add_rest_to_credits' => false,
            'use_client_balance' => false,
            'is_sponsor' => false,
            'is_shipping' => false
        ]);
    }
}
