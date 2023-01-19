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

        $this->validator($data)->validate();

        $payment->fill(Arr::only(
            $data,
            [
                'bank_uid',
                'note',
                'payment_via_id',
                'date'
            ]
        ));

        if ($this->isPaymentFromEntries($data)) {
            $payment->fillConfirmation();
        }

        $payment->save();

        return $payment;
    }

    private function isPaymentFromEntries($data)
    {
        return !empty($data['bank_uid']);
    }

    public function validator($data)
    {
        return Validator::make($data, [
            'note' => [
                'nullable',
                'max:191'
            ],
            'payment_via_id' => [
                'required',
                'exists:vias,id'
            ],
            'bank_uid' => [
                'nullable',
                'unique:payments',
                'exists:entries'
            ],
        ], $this->errorMessages());
    }
}
