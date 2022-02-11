<?php

namespace App\GraphQL\Queries;

use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class PaymentsPendencies
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        return Payment::pendencies()
            ->groupBy('created_at')
            ->orderBy(DB::raw('DATE(created_at)'), 'desc')
            ->get([
                DB::raw('DATE(created_at) as created_at_payment'),
                DB::raw('COUNT(*) as total'),
            ]);
    }
}
