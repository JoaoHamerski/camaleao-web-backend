<?php

namespace App\GraphQL\Mutations;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Arr;
use App\GraphQL\Traits\PaymentTrait;
use Illuminate\Support\Facades\Validator;

class PaymentUpdate
{
    use PaymentTrait;

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $data = $this->getFormattedData($args);
        $payment = Payment::find($data['id']);

        Validator::make($data, [
            'note' => ['nullable', 'max:191'],
            'payment_via_id' => ['required', 'exists:vias,id']
        ])->validate();

        $payment->update(Arr::only(
            $data,
            ['note', 'payment_via_id']
        ));

        return $payment;
    }
}