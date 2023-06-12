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

        // $this->validator($data)->validate();

        $data = $this->handleFilesUpload($data);
        $data = $this->evaluateOrderAttributes($data);


        $order = $client->orders()->create($data);
        $this->syncItems($data, $order);

        return;


        if (!$order->isPreRegistered()) {
            $this->handleCommissions($order);
        }

        if ($this->hasDownPayment($data)) {
            $this->createDownPayment($order, $data);
        }

        return $order;
    }

    public function syncItems($data, $order)
    {
        $clothes = collect($data['clothes']);
        $clothes->each(function ($cloth) use ($order) {
            $match = $this->findClothMatch($cloth);

            $order->clothes()->create([
                'cloth_match_id' => $match->id
            ]);
        });

        dd($order->load('clothes'));
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
            'note'  => 'Pagamento de entrada'
        ]);
    }
}
