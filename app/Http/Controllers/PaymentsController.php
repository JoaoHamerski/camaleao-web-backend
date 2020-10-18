<?php

namespace App\Http\Controllers;

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
        if ($order->is_closed)
            abort(403);
        
    	$validate = Validator::make($data = $this->getFormattedData($request->all()), [
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

    public function getFormattedData(array $data) 
    {
    	if (isset($data['value']))
    		$data['value'] = Sanitizer::removeNonDigits($data['value']);

    	if (isset($data['date']) && Validate::isDate($data['date']))
    		$data['date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $data['date'])->toDateString();

    	return $data;
    }
}
