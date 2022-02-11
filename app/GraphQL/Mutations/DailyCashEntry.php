<?php

namespace App\GraphQL\Mutations;

use App\Models\Order;
use App\Models\Client;
use App\Util\Formatter;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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

        return $order->payments()->create([
            'value' => $data['value'],
            'payment_via_id' => $data['via_id'],
            'date' => now(),
            'is_confirmed' => Auth::user()->hasRole('gerencia') ?: null
        ]);
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
            ->currencyBRL([
                'order.price',
                'value'
            ])
            ->name('client.name')
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
            'via_id' => ['required', 'exists:vias,id'],
            'value' => ['required', 'numeric']
        ];

        if (!$order && !empty($data['order']['price'])) {
            $rules['value'][] = 'max_currency:' . $data['order']['price'];
        }

        if ($order) {
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
            'client.name.required' => 'Por favor, informe o nome do cliente.',
            'client.id.required_without' => 'Por favor, selecione um cliente.',
            'order.code.required' => 'Por favor, informe o código do pedido.',
            'order.unique' => 'Este código já está sendo utilizado por outro pedido.',
            'order.id.required_without' => 'Por favor, selecione um pedido.',
            'order.id.required_with' => 'Por favor, selecione um pedido.',
            'order.value.required' => 'Por favor, informe o valor.',
            'order.price.required' => 'Por favor, informe o valor do pedido',
            'via_id.required' => 'Por favor, selecione uma via.',
            'value.max_currency' => $isNewOrder
                ? 'O pagamento não pode ser maior que o valor do pedido (:max).'
                : 'O pagamento não pode ser maior que o total restante (:max).',
        ];
    }
}
