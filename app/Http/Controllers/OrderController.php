<?php

namespace App\Http\Controllers;

use App\Filters\OrderFilter;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(Request $request)
    {
        if ($request->bearerToken() !== 'f969d0639f3a6e6d01003767968907079dc88e3b9a633e180249d060521fcb06') {
            abort(403);
        }
    }

    public function index(Request $request)
    {
        $request->validate([
            'per-page' => ['numeric', 'min:1']
        ]);

        $orders = Order::query();

        app(OrderFilter::class)->filter($orders, $request->all());

        return OrderResource::collection(
            $orders->paginate($request->input('per-page', 10))
        );
    }

    public function show(Order $order)
    {
        return new OrderResource($order);
    }
}
