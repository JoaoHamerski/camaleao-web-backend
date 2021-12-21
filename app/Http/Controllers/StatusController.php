<?php

namespace App\Http\Controllers;

use App\Http\Resources\StatusResource;
use App\Models\Client;
use App\Models\Order;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StatusController extends Controller
{
    public function index()
    {
        return StatusResource::collection(Status::all());
    }

    public function patch(Client $client, Order $order, Request $request)
    {
        Validator::make($request->all(), [
            'status' => 'required|exists:status,id'
        ])->validate();

        $order->update([
            'status_id' => $request->status
        ]);

        return response('', 200);
    }
}
