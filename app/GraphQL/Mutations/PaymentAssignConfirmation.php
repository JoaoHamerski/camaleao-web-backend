<?php

namespace App\GraphQL\Mutations;

use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class PaymentAssignConfirmation
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

        $payment->fill([
            'is_confirmed' => $args['confirmation'],
            'confirmed_at' => now()

        ]);

        if ($payment->sponsorshipClient) {
            $payment->date = Carbon::now()->toDateString();
        }

        $payment->save();

        return $payment;
    }
}
