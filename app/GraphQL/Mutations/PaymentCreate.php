<?php

namespace App\GraphQL\Mutations;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\GraphQL\Exceptions\UnprocessableException;
use App\GraphQL\Traits\PaymentTrait;

class PaymentCreate
{
    use PaymentTrait;

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $data = $this->getFormattedData($args);
        $order = Order::find($data['order_id']);

        if (!$order) {
            throw new UnprocessableException(
                'Não foi possível registrar o pagamento.',
                'O pedido especificado não existe.'
            );
        }

        Validator::make(
            $data,
            [
                'order_id' => ['required', 'exists:orders,id'],
                'payment_via_id' => ['required', 'exists:vias,id'],
                'value' => ['required', 'max_currency:' . $order->total_owing],
                'date' => ['required', 'date_format:Y-m-d'],
                'note' => ['max:255']
            ],
            $this->errorMessages(false)
        )->validate();

        $payment = $order->payments()->create($data);

        if (Auth::user()->hasRole('gerencia')) {
            $payment->confirm();
        }

        return $payment;
    }
}
