<?php

namespace App\GraphQL\Queries;

use Illuminate\Support\Facades\URL;

class OrdersWeeklyCalendarReport
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        return URL::temporarySignedRoute(
            'pdf.orders-weekly-calendar',
            now()->addMinutes(10),
            $args
        );
    }
}
