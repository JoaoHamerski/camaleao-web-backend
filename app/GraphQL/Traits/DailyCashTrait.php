<?php

namespace App\GraphQL\Traits;

use Carbon\Carbon;
use App\Models\Entry;
use App\Models\Order;
use App\Models\Client;
use App\Models\Payment;
use App\Util\Formatter;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Util\Helper;

trait DailyCashTrait
{
    public function validator($data, array $isNew)
    {
        return Validator::make(
            $data,
            $this->getRules(
                $isNew,
                $data,
                Arr::get($data, 'bank_uid')
            ),
            $this->errorMessages($isNew['order'])
        );
    }

    public function checkForNewClientAndOrder(array $data)
    {
        $clientId = Arr::get($data, 'client.id');
        $orderId = Arr::get($data, 'order.id');

        return [
            'client' => !Client::where('id', $clientId)->exists(),
            'order' => !Order::where([
                ['id', '=', $orderId],
                ['client_id', '=', $clientId]
            ])->exists()
        ];
    }

    public function getFilledPayment(array $data, $order = null, $payment = null)
    {
        $input = Helper::arrayOnly($data, [
            'value',
            'date',
            'note',
            'bank_uid',
            'sponsorship_client_id',
            'is_shipping',
        ], [
            'payment_via_id' => $data['via_id']
        ]);

        if ($payment) {
            $payment->fill($input);

            return $payment;
        }

        if (Arr::get($data, 'untied') || !$order) {
            return Payment::make($input);
        }

        return $order->payments()->make($input);
    }

    public function createEntry(array $data)
    {
        $input = Helper::arrayOnly($data, [
            'value',
            'via_id'
        ], [
            'bank_uid' => 'manual-' . (string) Str::uuid(),
            'date' => Carbon::createFromFormat(
                'Y-m-d',
                $data['date']
            )->format('d/m/Y'),
            'description' =>  $data['note'],
        ]);

        return Entry::create($input);
    }

    public function getRules(array $isNew, array $data, $exceptBankUid = null): array
    {
        $order = Order::find(Arr::get($data, 'order.id'));
        $rules = [];

        if (!$data['untied']) {
            $rules[] = $this->clientValidationRules($isNew['client']);
            $rules[] = $this->orderValidationRules($isNew['order']);
        }

        $rules[] = $this->paymentValidationRules($data, $order, $exceptBankUid);

        return Arr::collapse($rules);
    }

    public function getFormattedData(array $data)
    {
        $data['client']['id'] = Arr::get($data, 'client.id');
        $data['order']['id'] = Arr::get($data, 'order.id');

        return (new Formatter($data))
            ->currencyBRL(['order.price', 'value'])
            ->name('client.name')
            ->date('date')
            ->get();
    }

    public function clientValidationRules(bool $isNewClient): array
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

    public function orderValidationRules(bool $isNewOrder): array
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

    public function paymentValidationRules($data, $order = null, $exceptBankUid = null): array
    {
        $rules = [
            'bank_uid' => [
                'nullable',
                Rule::unique('payments')->ignore($exceptBankUid, 'bank_uid'),
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
            ],
            'untied' => [
                'required', 'boolean'
            ]
        ];

        if (!$order && !empty($data['order']['price'])) {
            $rules['value'][] = 'max_currency:' . $data['order']['price'];
        }

        if ($order) {
            $rules['sponsorship_client_id'][] = Rule::notIn($order->client->id);

            if (!$data['is_shipping']) {
                $rules['value'][] = 'max_currency:' . $order->total_owing;
            }
        }

        return $rules;
    }

    public function getClient(array $data, bool $isNew): Client
    {
        if ($isNew) {
            return Client::create([
                'name' => $data['client']['name']
            ]);
        }

        return Client::find($data['client']['id']);
    }

    public function getOrder(array $data, array $isNew): Order
    {
        $client = $this->getClient($data, $isNew['client']);

        if ($isNew['order']) {
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

    private static function errorMessages($isNewOrder)
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
