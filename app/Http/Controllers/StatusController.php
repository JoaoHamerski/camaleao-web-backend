<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Order;
use App\Models\Status;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    public function patch(Client $client, Order $order, Request $request)
    {
    	if (Status::where('id', $request->status)->exists()) {
    		$order->status_id = $request->status;
    		$order->save();
    	}

    	return redirect($order->path());
    }
}
