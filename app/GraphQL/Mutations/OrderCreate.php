<?php

namespace App\GraphQL\Mutations;

use App\Models\Client;
use Illuminate\Support\Arr;
use App\GraphQL\Traits\OrderTrait;
use Illuminate\Support\Facades\Auth;

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

        $this->validator($data)->validate();

        $data = $this->handleFilesUpload($data);

        $order = $client->orders()->create($data);

        $order->clothingTypes()->attach(
            $this->getFilledClothingTypes($data)
        );

        if (!$order->isPreRegistered()) {
            $this->handleCommissions($order);
        }

        if (!empty($data['down_payment']) && !empty($data['payment_via_id'])) {
            $payment = $order->createDownPayment(
                $data['down_payment'],
                $data['payment_via_id']
            );

            if (Auth::user()->hasRole('gerencia')) {
                $payment->confirm();
            }
        }

        return $order;
    }
}
