<?php

namespace App\GraphQL\Mutations;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Client;
use App\Models\Payment;
use App\Util\Formatter;
use App\Models\BankEntry;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\GraphQL\Exceptions\UnprocessableException;

class DailyCashEntry
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $data = $this->getFormattedData($args);

        $isNew['client'] = !Client::where('id', $data['client']['id'])->exists();
        $isNew['order'] = !Order::where([
            ['id', '=', $data['order']['id']],
            ['client_id', '=', $data['client']['id']]
        ])->exists();

        Validator::make(
            $data,
            $this->getRules($isNew, $data),
            $this->errorMessages($isNew['order'])
        )->validate();

        $order = $this->getOrder(
            $data,
            $this->getClient($data, $isNew['client']),
            $isNew['order']
        );

        return $this->createPayment($data, $order);
    }

    public function createPayment(array $data, Order $order)
    {
        $payment = $order->payments()->make(Arr::only($data, [
            'value',
            'date',
            'note',
            'bank_uid',
            'sponsorship_client_id'
        ]));

        $payment->payment_via_id = $data['via_id'];

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

    public function getRules(array $isNew, array $data): array
    {
        $order = Order::find($data['order']['id']);

        $rules = [];
        $rules[] = $this->clientValidatorRules($isNew['client']);
        $rules[] = $this->orderValidatorRules($isNew['order']);
        $rules[] =  $this->paymentValidationRules($data, $order);

        return Arr::collapse($rules);
    }

    public function getFormattedData(array $data)
    {
        $data['client']['id'] = isset($data['client']['id']) ? $data['client']['id'] : '';
        $data['order']['id'] = isset($data['order']['id']) ? $data['order']['id'] : '';

        return (new Formatter($data))
            ->currencyBRL(['order.price', 'value'])
            ->name('client.name')
            ->date('date')
            ->get();
    }

    private function clientValidatorRules($isNewClient)
    {
        if ($isNewClient) {
            return [
                'client.id' => ['required_without:client.name'],
                'client.name' => ['required']
            ];
        }

        return [
            'client' => ['required', 'array'],
            'client.id' => ['required', 'exists:clients,id']
        ];
    }

    private function orderValidatorRules($isNewOrder)
    {
        if ($isNewOrder) {
            return [
                'order.id' => ['required_without:order.code'],
                'order.code' => ['required', 'unique:orders,code'],
                'order.price' => ['required', 'numeric', 'min:0.01'],
            ];
        }

        return [
            'order' => ['required', 'array'],
            'order.id' => ['required', 'exists:orders,id']
        ];
    }

    public function paymentValidationRules($data, $order = null)
    {
        $rules = [
            'bank_uid' => [
                'nullable',
                'unique:payments',
                'exists:entries'
            ],
            'via_id' => ['required', 'exists:vias,id'],
            'value' => ['required', 'numeric'],
            'date' => [
                'nullable',
                'required_if:is_sponsor,false',
                'date',
                'before_or_equal:' . Carbon::now()->toDateString()
            ],
            'sponsorship_client_id' => [
                'nullable',
                'exists:clients,id',
                Rule::requiredIf($data['is_sponsor']),
            ]
        ];

        if (!$order && !empty($data['order']['price'])) {
            $rules['value'][] = 'max_currency:' . $data['order']['price'];
        }

        if ($order) {
            $rules['sponsorship_client_id'][] = Rule::notIn($order->client->id);
            $rules['value'][] = 'max_currency:' . $order->total_owing;
        }

        return $rules;
    }

    public function getClient(array $data, bool $isNew)
    {
        if ($isNew) {
            return Client::create([
                'name' => $data['client']['name']
            ]);
        }

        return Client::find($data['client']['id']);
    }

    public function getOrder(array $data, Client $client = null, $isNewOrder)
    {
        if ($isNewOrder) {
            $order = $client->orders()->create([
                'code' => $data['order']['code'],
                'price' => $data['order']['price']
            ]);

            if (!empty($data['order']['reminder'])) {
                $order->notes()->create([
                    'text' => $data['order']['reminder'],
                    'is_reminder' => true
                ]);
            }

            return $order;
        }

        return $client->orders()->find($data['order']['id']);
    }

    private function errorMessages($isNewOrder)
    {
        return [
            'date.required_if' => __('validation.rules.required'),
            'client.name.required' => __('validation.rules.required'),
            'client.id.required_without' => __('validation.rules.required_list', [
                'pronoun' => 'um',
                'attribute' => 'cliente'
            ]),
            'order.code.required' => __('validation.rules.required'),
            'order.code.unique' => __('validation.rules.unique', ['pronoun' => 'O']),
            'order.id.required_without' => __('validation.rules.required_list', [
                'pronoun' => 'um',
                'attribute' => 'pedido'
            ]),
            'order.price.required' => __('validation.rules.required'),
            'via_id.required' => __('validation.rules.required_list', ['pronoun' => 'a']),
            'value.required' => __('validation.rules.required'),
            'value.max_currency' => $isNewOrder
                ? __('validation.rules.max_currency', [
                    'attribute' => 'pagamento',
                    'subject' => 'valor do pedido'
                ])
                : __('validation.rules.max_currency', [
                    'attribute' => 'pagamento',
                    'subject' => 'total restante'
                ]),
            'sponsorship_client_id.not_in' => __('validation.custom.payments.sponsorship_client_id|not_in'),
            'sponsorship_client_id.required' => __('validation.rules.required'),
            'before_or_equal' => __('validation.rules.before_or_equal_today'),
            'bank_uid.unique' => __('validation.custom.payments.unique')
        ];
    }
}
