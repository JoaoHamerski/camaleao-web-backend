<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Via;
use App\Models\Order;
use App\Models\Client;
use App\Util\Validate;
use App\Models\Payment;
use App\Util\Sanitizer;
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
    
    public function getPaymentsOfDay(Request $request)
    {
        $payments = Payment::with(['order', 'order.client', 'via']);
        $date = Carbon::now()->toDateString();

        if ($request->filled('date')) {
            $date = $request->date;
        }

        $payments = $payments->whereDate(
            'created_at',
            $date
        )->orderBy('created_at', 'desc');

        if ($request->filled('only_pendency') && $request->only_pendency === 'true') {
            $payments = $payments->whereNull('is_confirmed');
        }
        
        return response()->json([
            'payments' => $payments->get()
        ], 200);
    }

    public function getPendencies()
    {
        $pendencies = Payment::pendencies()
            ->groupBy('date_registered')
            ->orderBy(DB::raw('DATE(created_at)'), 'desc')
            ->get([
                DB::raw('DATE(created_at) as date_registered'),
                DB::raw('COUNT(*) as total'),
            ]);

        return response()->json([
            'pendencies' => $pendencies
        ], 200);
    }

    public function assignConfirmation(Request $request, Payment $payment)
    {
        Validator::make($request->all(), [
            'confirmation' => ['required', 'boolean']
        ])->validate();
        
        if ($request->confirmation === true) {
            $validator = Validator::make($payment->toArray(), [
                'value' => ['required', 'max_double:' . $payment->order->getTotalOwing()]
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'totalOwing' => $payment->order->getTotalOwing(),
                    'payment' => $payment->value
                ], 422);
            }
        }

        $payment->update([
            'is_confirmed' => $request->confirmation,
            'confirmed_at' => now()
        ]);

        return response()->json([], 204);
    }

    public function getTotalPendencies()
    {
        return response()->json([
            'totalPendencies' => Payment::pendencies()->count()
        ], 200);
    }

    private function errorMessages($isNewOrder)
    {
        return [
            'client.required' => 'Por favor, digite o nome do cliente.',
            'client.id.required' => 'Por favor, selecione um cliente.',
            'order.required' => 'Por favor, informe o código do pedido.',
            'order.unique' => 'Este código já está sendo utilizado por outro pedido.',
            'order.id.required' => 'Por favor, selecione um pedido.',
            'order.id.required_with' => 'Por favor, selecione um pedido.',
            'order_value.required' => 'Por favor, informe o valor.',
            'order.not_regex' => 'O código deve conter apenas letras, numeros ou traços.',
            'value.lte' => $isNewOrder
                ? 'O pagamento não pode ser maior que o valor do pedido.'
                : 'O pagamento não pode ser maior que o total restante.',
            'via_id.required' => 'Por favor, informe a via.',
        ];
    }

    private function clientValidatorRules($isNewClient)
    {
        if ($isNewClient) {
            return [
                'client' => ['required']
            ];
        }

        return [
            'client' => ['array'],
            'client.id' => ['required', 'exists:clients,id']
        ];
    }

    private function orderValidatorRules($isNewOrder)
    {
        if ($isNewOrder) {
            return [
                'order' => ['required', 'unique:orders,code', 'not_regex:/[^a-z\-0-9]/i'],
                'order_value' => ['required', 'numeric', 'min:0.01'],
            ];
        }

        return [
            'order' => ['array'],
            'order.id' => ['required_with:client.id', 'exists:orders,id']
        ];
    }

    public function dailyPayment(Request $request)
    {
        function getClient(array $data)
        {
            if ($data['isNewClient']) {
                return Client::create([
                    'name' => $data['client']
                ]);
            }
                
            return Client::find($data['client']['id']);
        }

        function getOrder(array $data, Client $client)
        {
            if ($data['isNewOrder']) {
                $order = $client->orders()->create([
                    'code' => $data['order'],
                    'price' => $data['order_value']
                ]);

                if (! empty($data['reminder'])) {
                    $order->notes()->create([
                        'text' => $data['reminder'],
                        'is_reminder' => true
                    ]);
                }

                return $order;
            }

            return $client->orders()->find($data['order']['id']);
        }

        $data = $this->getFormattedData($request->all());
        
        $rules = [];
        $rules[] = $this->clientValidatorRules($data['isNewClient']);
        $rules[] = $this->orderValidatorRules($data['isNewOrder']);
        $rules[] =  [
            'value' => [
                'required',
                'numeric',
                $data['isNewOrder'] ? 'lte:order_value' : 'lte:order.total_owing'],
            'via_id' => ['required', 'exists:vias,id']
        ];
        
        $rules = Arr::collapse($rules);

        Validator::make(
            $data,
            $rules,
            $this->errorMessages($data['isNewOrder'])
        )->validate();

        $order = getOrder($data, getClient($data));

        $order->payments()->create([
            'value' => $data['value'],
            'payment_via_id' => $data['via_id'],
            'date' => now(),
            'is_confirmed' => Auth::user()->isAdmin() ? true : null
        ]);

        return response()->json([], 204);
    }

    public function store(Request $request, Client $client, Order $order)
    {
        if ($order->is_closed) {
            abort(403);
        }
        
        Validator::make($data = $this->getFormattedData($request->all()), [
            'payment_via_id' => 'required|exists:vias,id',
            'value' => 'required|max_double:' . $order->getTotalOwing(),
            'date' => 'required|date_format:Y-m-d'
        ])->validate();

        if (Auth::user()->hasRole('gerencia')) {
            $data['confirmed_at'] = Carbon::now();
            $data['is_confirmed'] = true;
        }
 
        $order->payments()->create($data);

        return response()->json([
            'message' => 'success',
            'redirect' => $order->path()
        ], 200);
    }

    public function patch(Client $client, Order $order, Payment $payment, Request $request)
    {
        if ($payment->is_confirmed !== null) {
            abort(403);
        }

        Validator::make($request->all(), [
            'note' => 'nullable|max:191',
            'payment_via_id' => 'required|exists:vias,id'
        ])->validate();

        $payment->update($request->only([
            'payment_via_id', 'note'
        ]));

        return response()->json([
            'message' => 'success',
            'redirect' => $order->path()
        ], 200);
    }

    public function getChangePaymentView(Client $client, Order $order, Payment $payment)
    {
        return response()->json([
            'message' => 'success',
            'view' => view('orders.partials.payment-form', [
                'payment' => $payment,
                'vias' => Via::all(),
                'method' => 'PATCH'
            ])->render()
        ], 200);
    }

    public function getFormattedData(array $data)
    {
        if (isset($data['value'])) {
            $data['value'] = Sanitizer::money($data['value']);
        }
        
        if (isset($data['order_value'])) {
            $data['order_value'] = Sanitizer::money($data['order_value']);
        }

        if (isset($data['date']) && Validate::isDate($data['date'])) {
            $data['date'] = Carbon::createFromFormat(
                'd/m/Y',
                $data['date']
            )->toDateString();
        }

        return $data;
    }
}
