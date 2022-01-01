<?php

namespace App\Http\Controllers;

use App\Http\Resources\PaymentResource;
use Carbon\Carbon;
use App\Models\Via;
use App\Models\Order;
use App\Models\Client;
use App\Util\Validate;
use App\Models\Payment;
use App\Queries\PaymentsRequest;
use App\Util\Formatter;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PaymentsController extends Controller
{
    public function index()
    {
        return view('payments.index');
    }

    public function store(Request $request, Client $client, Order $order)
    {
        if ($order->is_closed) {
            abort(403);
        }

        $data = $this->getFormattedData($request->all());

        Validator::make(
            $data,
            [
                'payment_via_id' => ['required', 'exists:vias,id'],
                'value' => ['required', 'max_currency:' . $order->getTotalOwing()],
                'date' => ['required', 'date_format:Y-m-d'],
                'note' => ['max:255']
            ],
            $this->errorMessages(false)
        )->validate();

        $payment = $order->payments()->create($data);

        if (Auth::user()->hasRole('gerencia')) {
            $payment->confirm();
        }

        return response('', 200);
    }

    public function update(Client $client, Order $order, Payment $payment, Request $request)
    {
        if ($payment->is_confirmed !== null) {
            abort(403);
        }

        Validator::make($request->all(), [
            'note' => ['nullable', 'max:191'],
            'payment_via_id' => ['required', 'exists:vias,id']
        ])->validate();

        $payment->update($request->only([
            'payment_via_id',
            'note'
        ]));

        return response('', 200);
    }

    public function getFormattedData(array $data)
    {
        return Formatter::parse($data, [
            'parseCurrencyBRL' => [
                'value',
                'price'
            ],
            'parseDate' => [
                'date'
            ]
        ]);
    }

    private function errorMessages($isNewOrder)
    {
        return [
            'client.required' => 'Por favor, informe o nome do cliente.',
            'client.id.required' => 'Por favor, selecione um cliente.',
            'order.required' => 'Por favor, informe o código do pedido.',
            'order.unique' => 'Este código já está sendo utilizado por outro pedido.',
            'order.id.required' => 'Por favor, selecione um pedido.',
            'order.id.required_with' => 'Por favor, selecione um pedido.',
            'order_value.required' => 'Por favor, informe o valor.',
            'via_id.required' => 'Por favor, selecione uma via.',
            'value.max_currency' => $isNewOrder
                ? 'O pagamento não pode ser maior que o valor do pedido (:max).'
                : 'O pagamento não pode ser maior que o total restante (:max).',
        ];
    }

    public function paymentsOfDay(Request $request)
    {
        Validator::make($request->all(), [
            'date' => ['nullable', 'date_format:d/m/Y']
        ])->validate();

        if (!$request->filled('date')) {
            $request->merge(['date' => Carbon::now()->toDateString()]);
        } else {
            $request->merge([
                'date' => Carbon::createFromFormat(
                    'd/m/Y',
                    $request->date
                )->toDateString()
            ]);
        }

        $payments = PaymentsRequest::query($request);

        return PaymentResource::collection($payments->get());
    }

    public function pendencies()
    {
        $pendencies = Payment::pendencies()
            ->groupBy('payment_date')
            ->orderBy(DB::raw('DATE(created_at)'), 'desc')
            ->get([
                DB::raw('DATE(created_at) as payment_date'),
                DB::raw('COUNT(*) as total'),
            ]);

        return response()->json([
            'data' => $pendencies
        ], 200);
    }

    public function confirmPayment(Request $request, Payment $payment)
    {
        Validator::make($request->all(), [
            'confirmation' => ['required', 'boolean']
        ])->validate();

        if ($request->confirmation === true) {
            Validator::make($payment->toArray(), [
                'value' => [
                    'required',
                    'max_currency:' . $payment->order->getTotalOwing()
                ]
            ])->validate();
        }

        $payment->update([
            'is_confirmed' => $request->confirmation,
            'confirmed_at' => now()
        ]);

        return response()->json([], 204);
    }

    private function clientValidatorRules($isNewClient)
    {
        if ($isNewClient) {
            return [
                'client' => ['required']
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
                'order' => ['required', 'unique:orders,code'],
                'price' => ['required', 'numeric', 'min:0.01'],
            ];
        }

        return [
            'order' => ['required', 'array'],
            'order.id' => ['required_with:client.id', 'exists:orders,id']
        ];
    }

    public function paymentValidationRules($isNewOrder, $data)
    {
        $rules = [
            'via_id' => ['required', 'exists:vias,id'],
            'value' => [
                'required',
                'numeric'
            ]
        ];

        if ($isNewOrder && isset($data['price'])) {
            $rules['value'][] = 'max_currency:' . $data['price'];
        }

        if (!$isNewOrder && isset($data['order']['total_owing'])) {
            $rules['value'][] = 'max_currency:' . $data['order']['total_owing'];
        }

        return $rules;
    }

    public function getClient(array $data)
    {
        if ($data['isNewClient']) {
            return Client::create([
                'name' => $data['client']
            ]);
        }

        return Client::find($data['client']['id']);
    }

    public function getOrder(array $data, Client $client)
    {
        if ($data['isNewOrder']) {
            $order = $client->orders()->create([
                'code' => $data['order'],
                'price' => $data['price']
            ]);

            if (!empty($data['reminder'])) {
                $order->notes()->create([
                    'text' => $data['reminder'],
                    'is_reminder' => true
                ]);
            }

            return $order;
        }

        return $client->orders()->find($data['order']['id']);
    }

    public function dailyCashStore(Request $request)
    {
        $data = $this->getFormattedData($request->all());

        $rules = [];
        $rules[] = $this->clientValidatorRules($data['isNewClient']);
        $rules[] = $this->orderValidatorRules($data['isNewOrder']);
        $rules[] =  $this->paymentValidationRules($data['isNewOrder'], $data);

        $rules = Arr::collapse($rules);

        Validator::make(
            $data,
            $rules,
            $this->errorMessages($data['isNewOrder'])
        )->validate();

        $order = $this->getOrder($data, $this->getClient($data));

        $order->payments()->create([
            'value' => $data['value'],
            'payment_via_id' => $data['via_id'],
            'date' => now(),
            'is_confirmed' => Auth::user()->isAdmin() ? true : null
        ]);

        return response()->json([], 204);
    }
}
