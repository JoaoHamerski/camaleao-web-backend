<?php

namespace App\GraphQL\Mutations;

use App\Models\Payment;
use Illuminate\Support\Facades\Validator;

class PaymentConfirm
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'id' => ['required', 'exists:payments,id'],
            'confirmation' => ['required', 'boolean']
        ])->validate();

        $payment = Payment::find($args['id']);

        if ($args['confirmation']) {
            Validator::make($payment->toArray(), [
                'value' => [
                    'required',
                    'max_currency:' . $payment->order->total_owing
                ]
            ])->validate();
        }

        $payment->update([
            'is_confirmed' => $args['confirmation'],
            'confirmed_at' => now()
        ]);

        return $payment;
    }
}
