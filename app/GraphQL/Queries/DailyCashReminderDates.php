<?php

namespace App\GraphQL\Queries;

use App\Models\DailyCashReminder;
use Illuminate\Support\Facades\DB;

class DailyCashReminderDates
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        return DailyCashReminder::groupBy('date')
            ->orderBy('created_at', 'desc')
            ->get([
                'date',
                DB::raw('COUNT(*) as total')
            ]);
    }
}
