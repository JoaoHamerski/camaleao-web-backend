<?php

namespace App\GraphQL\Mutations;

use Carbon\Carbon;
use App\Models\Entry;
use App\Models\Order;
use Illuminate\Validation\Rule;
use App\GraphQL\Traits\PaymentTrait;
use Illuminate\Support\Facades\Validator;
use App\GraphQL\Exceptions\UnprocessableException;
use App\Traits\EntriesTrait;

class PaymentCreate
{
    use EntriesTrait, PaymentTrait;

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $data = $this->getFormattedData($args);
        $order = Order::find($data['order_id']);

        $this->validator($data, $order)->validate();

        return $this->createPayment($data, $order);
    }

    public function createPayment(array $data, Order $order)
    {
        $payment = $order->payments()->make($data);

        if ($this->isFromEntries($data) && !$this->isValidEntry($data)) {
            throw new UnprocessableException(
                'Dados inválidos.',
                'Os dados informados diferem da entrada bancária.'
            );
        }

        if ($payment->isConfirmable($data)) {
            $payment->makeConfirm();
        }

        if ($payment->sponsorship_client_id) {
            $payment->date = null;
        }

        $payment->save();

        return $payment;
    }

    public function validator($data, $order)
    {
        if (!$order) {
            throw new UnprocessableException(
                'Não foi possível registrar o pagamento.',
                'O pedido especificado não existe.'
            );
        }

        return Validator::make(
            $data,
            [
                'bank_uid' => [
                    'nullable',
                    'unique:payments',
                    'exists:entries,bank_uid'
                ],
                'is_sponsor' => ['required', 'boolean'],
                'order_id' => ['required', 'exists:orders,id'],
                'payment_via_id' => ['required', 'exists:vias,id'],
                'value' => ['required', 'min_currency:0.01', 'max_currency:' . $order->total_owing],
                'date' => [
                    'nullable',
                    'required_if:is_sponsor,false',
                    'date',
                    'before_or_equal:' . Carbon::now()->toDateString()
                ],
                'note' => ['nullable', 'max:255'],
                'sponsorship_client_id' => [
                    'nullable',
                    'exists:clients,id',
                    Rule::notIn($order->client->id),
                    Rule::requiredIf($data['is_sponsor'])
                ]
            ],
            $this->errorMessages()
        );
    }
}
