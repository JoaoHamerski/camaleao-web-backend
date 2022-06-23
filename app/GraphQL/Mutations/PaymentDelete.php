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

        $payment = Payment::find($args['id']);
        $payment->delete();

        return $payment;
    }

    public function validator(array $data)
    {
        return Validator::make($data, [
            'id' => ['required', 'exists:payments,id'],
            'password' => ['required', 'current_password']
        ]);
    }
}
