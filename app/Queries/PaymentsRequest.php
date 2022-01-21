<?php

namespace App\Queries;

use App\Util\Helper;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentsRequest
{
    public static function query(Request $request, $options = [])
    {
        $request = $request->duplicate();

        $data = $options['data'] ?? [];
        $merge = $options['merge'] ?? [];
        $payments = $options['query'] ?? Payment::query();
        $isPDF = $options['isPDF'] ?? false;

        if (!empty($merge)) {
            $request->merge($merge);
        }

        if (Helper::parseBool($request->only_pendency)) {
            $payments->whereNull('is_confirmed');
        }

        if ($request->filled('date')) {
            $payments->whereDate(
                'created_at',
                $request->date
            )->orderBy('created_at', 'desc');
        }

        if ($request->filled('start_date') && $request->isNotFilled('end_date')) {
            $payments->whereDate('date', $data['start_date']);
        }

        if ($request->filled(['start_date', 'end_date'])) {
            $payments->whereBetween('date', [
                $data['start_date'] ?? $request->start_date,
                $data['end_date'] ?? $request->end_date
            ]);
        }

        if ($request->filled('description')) {
            $payments->whereHas('order', function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->description . '%');
            });
        }

        return $payments;
    }
}
