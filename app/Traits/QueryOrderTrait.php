<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait QueryOrderTrait
{
    public function queryOrders($orders, $data, Request $request)
    {
        if ($request->filled('city_id')) {
            $orders->whereHas('client.city', function ($query) use ($data) {
                $query->where('id', $data['city_id']);
            });
        }

        if ($request->filled('status_id')) {
            if (is_array($data['status_id'])) {
                $orders->whereIn('status_id', $data['status_id']);
            } else {
                $orders->where('status_id', $data['status_id']);
            }
        }

        if ($request->filled('closed_at')) {
            $orders->whereDate('closed_at', $data['closed_at']);
        }

        if ($request->filled('delivery_date')) {
            $orders->whereDate('delivery_date', $data['delivery_date']);
        }

        if ($request->filled('print_date')) {
            $orders->whereDate('print_date', $data['print_date']);
        }

        if ($request->filled('seam_date')) {
            $orders->whereDate('seam_date', $data['seam_date']);
        }

        if ($request->filled('order')) {
            $orders = $this->queryOrdersOrder($orders, $data['order']);
        }

        if ($request->filled('state')) {
            $orders = $this->queryOrdersState($orders, $data['state']);
        }

        return $orders;
    }

    public function queryOrdersOrder($query, $order)
    {
        if ($order === 'older') {
            $query->orderBy('created_at', 'ASC');
        }

        if ($order === 'newer') {
            $query->orderBy('created_at', 'DESC');
        }

        if ($order === 'delivery_date') {
            $query->orderBy('delivery_date', 'DESC');
        }

        return $query;
    }

    public function queryOrdersState($query, $state)
    {
        if ($state === 'open') {
            $query->whereNull('closed_at');
        }

        if ($state === 'all') {
            //
        }

        return $query;
    }
}
