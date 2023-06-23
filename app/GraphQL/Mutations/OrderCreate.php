<?php

namespace App\GraphQL\Mutations;

use Carbon\Carbon;
use App\Models\Client;
use App\GraphQL\Traits\OrderTrait;

class OrderCreate
{
    use OrderTrait;

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $data = $this->getFormattedData($args);

        $client = Client::find($args['client_id']);

        $data = $this->evaluateOrderAttributes($data);

        $this->validator($data)->validate();

        $data = $this->handleFilesUpload($data);

        $order = $client->orders()->create($data);

        $this->syncItems($data, $order);

        return $order;
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
