<?php

namespace App\GraphQL\Queries;

use App\Util\Formatter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class OrdersReportPrintDate
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $data = (new Formatter($args))
            ->date('date')
            ->get();

        Validator::make($data, [
            'state' => ['required', Rule::in(['all', 'open'])],
            'date' => ['required', 'date']
        ])->validate();

        return URL::temporarySignedRoute(
            'pdf.orders-report-print-date',
            now()->addMinutes(10),
            array_filter($data, fn ($item) => $item !== '')
        );
    }
}
