<?php

namespace App\Queries;

use App\Util\Helper;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentsRequest
{
    public static function query(Request $request, $payments = null)
    {
        if (!$payments) {
            $payments = Payment::query();
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

        return $payments;
    }
}
