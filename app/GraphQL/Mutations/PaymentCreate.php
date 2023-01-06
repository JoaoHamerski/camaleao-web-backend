<?php

namespace App\GraphQL\Mutations;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Payment;
use App\Traits\EntriesTrait;
use Illuminate\Validation\Rule;
use App\GraphQL\Traits\PaymentTrait;
use Illuminate\Support\Facades\Validator;
use App\GraphQL\Exceptions\UnprocessableException;

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

    public function getPaymentValue($totalValue, $totalOwing)
    {
        return $totalValue > $totalOwing
            ? $totalOwing
            : $totalValue;
    }

    public function createPayment(array $data, Order $order)
    {
        if ($this->isFromEntries($data) && !$this->isValidEntry($data)) {
            throw new UnprocessableException(
                'Dados inválidos.',
                'Os dados informados diferem da entrada bancária.'
            );
        }

        $data = $this->getComputedValues($order, $data);

        $payment = $order->payments()->make($data);

        if ($payment->isConfirmable($data)) {
            $payment->makeConfirm();
        }

        if ($payment->sponsorship_client_id) {
            $payment->date = null;
        }

        $payment->save();

        if (!$payment->is_shipping) {
            $this->createClientBalances($data, $payment);
        }

        return $payment;
    }

    public function getComputedValues(Order $order, $data)
    {
        $data['total_owing'] = $order->total_owing;
        $data['total_value'] = bcadd($data['value'], $data['credit'], 2);
        $data['original_value'] = $data['value'];

        $data['value'] = $this->getPaymentValue(
            $data['total_value'],
            $data['total_owing']
        );

        return $data;
    }

    public function createClientBalances($data, $payment)
    {
        if ($data['use_client_balance']) {
            $payment->clientBalances()->create([
                'is_confirmed' => $payment->is_confirmed,
                'client_id' => $payment->order->client->id,
                'value' => -$data['credit']
            ]);
        }

        if (
            $this->paymentExceedsTotalOwing(
                $data['original_value'],
                $data['total_owing']
            )
        ) {
            $payment->clientBalances()->create([
                'is_confirmed' => $payment->is_confirmed,
                'client_id' => $payment->sponsorship_client_id ?? $payment->order->client->id,
                'value' => bcsub($data['original_value'], $payment->value, 2)
            ]);
        }
    }

    public function paymentExceedsTotalOwing($value, $totalOwing)
    {
        return $value > $totalOwing;
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
                'order_id' => ['required', 'exists:orders,id'],
                'payment_via_id' => ['required', 'exists:vias,id'],
                'bank_uid' => [
                    'nullable',
                    'unique:payments',
                    'exists:entries'
                ],
                'note' => ['nullable', 'max:255'],
                'add_rest_to_credits' => ['required', 'boolean'],
                'use_client_balance' => ['required', 'boolean'],
                'credit' => $this->getCreditRules($order),
                'value' => $this->getValueRules($data, $order),
                'is_sponsor' => ['required', 'boolean'],
                'is_shipping' => ['required', 'boolean'],
                'sponsorship_client_id' => [
                    'nullable',
                    'exists:clients,id',
                    'required_if:is_sponsor,true',
                    Rule::notIn($order->client->id),
                ],
                'date' => [
                    'nullable',
                    'required_if:is_sponsor,false',
                    'date',
                    'before_or_equal:' . Carbon::now()->toDateString()
                ],
            ],
            $this->errorMessages()
        );
    }

    public function getValueRules($data, $order)
    {
        $rules = [];

        if (empty($data['credit'])) {
            $rules[] = 'required';
            $rules[] = 'min_currency:0.01';
        }

        if (!$data['add_rest_to_credits'] && !$data['is_shipping']) {
            $rules[] = 'max_currency:' . $order->total_owing;
        }

        return $rules;
    }

    public function getCreditRules($order)
    {
        $rules = [];
        $rules[] = 'nullable';
        $rules[] = 'max_currency:' . min($order->client->balance, $order->total_owing);
        $rules[] = 'required_if:use_client_balance,true';

        return $rules;
    }
}
