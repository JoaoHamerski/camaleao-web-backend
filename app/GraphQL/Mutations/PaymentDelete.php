<?php

namespace App\GraphQL\Mutations;

use App\Models\Payment;
use Illuminate\Support\Facades\Validator;

class PaymentDelete
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $this->validator($args)->validate();

        $payment = Payment::with('order.client')->find($args['id']);
        $payment->delete();

        if ($payment->is_bonus) {
            $this->addBonusBackToClient($payment);
        }

        return $payment;
    }

    public function addBonusBackToClient($payment)
    {
        $client = $payment->order->client;

        $client->update([
            'bonus' => bcadd($client->bonus, $payment->value)
        ]);
    }

    public function validator(array $data)
    {
        return Validator::make($data, [
            'id' => ['required', 'exists:payments,id'],
            'password' => ['required', 'current_password']
        ]);
    }
}
