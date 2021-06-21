<?php

namespace App\Http\Controllers;

use App\Models\Via;
use App\Util\Validate;
use App\Models\Order;
use App\Models\Client;
use App\Models\Payment;
use App\Util\Sanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentsController extends Controller
{
    public function store(Client $client, Order $order, Request $request)
    {
        if ($order->is_closed) {
            abort(403);
        }
        
        $validate = Validator::make($data = $this->getFormattedData($request->all()), [
            'payment_via_id' => 'required|exists:vias,id',
            'value' => 'required|max_double:' . $order->getTotalOwing(),
            'date' => 'required|date_format:Y-m-d'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'error',
                'errors' => $validate->errors()
            ], 422);
        }

        $order->payments()->create($data);

        return response()->json([
            'message' => 'success',
            'redirect' => $order->path()
        ], 200);
    }

    public function patch(Client $client, Order $order, Payment $payment, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'note' => 'nullable|max:191',
            'payment_via_id' => 'required|exists:vias,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

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

        if (isset($data['date']) && Validate::isDate($data['date'])) {
            $data['date'] = \Carbon\Carbon::createFromFormat(
                'd/m/Y',
                $data['date']
            )->toDateString();
        }

        return $data;
    }
}
