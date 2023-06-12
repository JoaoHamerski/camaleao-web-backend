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

        if (!$order->isPreRegistered()) {
            $this->handleCommissions($order);
        }

        if ($this->hasDownPayment($data)) {
            $this->createDownPayment($order, $data);
        }

        return $order;
    }

    public function syncItems($input, $order)
    {
        $inputGarments = collect($input['garments']);
        $inputGarments->each(function ($inputGarment) use ($order) {
            $match = $this->findGarmentMatch($inputGarment);
            $items = $inputGarment['items'];

            $garment = $order->garments()
                ->create(['garment_match_id' => $match->id]);

            $this->syncGarmentSizes($garment, $items);
        });
    }

    public function syncGarmentSizes($garment, $sizes)
    {
        foreach ($sizes as $size) {
            $garment
                ->sizes()
                ->attach($size['size_id'], [
                    'quantity' => $size['quantity']
                ]);
        }
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
