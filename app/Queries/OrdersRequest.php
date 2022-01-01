<?php

namespace App\Queries;

use App\Util\Helper;
use App\Models\Order;
use App\Models\Status;
use App\Util\Formatter;
use Illuminate\Http\Request;

class OrdersRequest
{
    public static function sortByOptions(Request $request, $orders)
    {
        if ($request->sort === 'priority') {
            $orders->whereNull('closed_at');
        }

        if ($request->sort === 'older') {
            $orders->orderBy('created_at');
        }

        if ($request->sort === 'newer') {
            $orders->latest();
        }

        if ($request->sort === 'production_date') {
            $orders->whereNull('closed_at');
            $orders->orderByRaw('CASE WHEN production_date IS NULL THEN 1 ELSE 0 END, production_date');
        }

        if ($request->sort === 'pre_register') {
            $orders->preRegistered();
        }

        return $orders;
    }

    public static function query($request, $orders = null, $isPDF = false)
    {
        if (!$orders) {
            $orders = Order::query();
        }

        $orders = static::sortByOptions($request, $orders);

        if ($isPDF) {
            $orders = Order::whereNotNull('quantity');
            $orders = Order::whereNotNull('client_id');
        }

        if ($request->filled('city')) {
            $orders->whereHas('client.city', function ($query) use ($request) {
                $query->where('id', $request->city);
            });
        }

        if (Helper::parseBool($request->pre_register)) {
            $orders->preRegistered();
        }

        if ($request->order === 'is_open') {
            $orders->whereNull('closed_at');
        }

        if ($request->filled('status') && Status::where('id', $request->status)->exists()) {
            $orders->where('status_id', $request->status);
        }

        if ($request->filled('code')) {
            $orders->where('code', 'like', '%' . $request->code . '%');
        }

        if ($request->filled('closing_date')) {
            $orders->whereDate(
                'closed_at',
                Formatter::parseDate($request->closing_date)
            );
        }

        if ($request->filled('delivery_date')) {
            $orders->whereDate(
                'delivery_date',
                Formatter::parseDate($request->delivery_date)
            );
        }

        return $orders;
    }
}
