<?php

namespace App\GraphQL\Queries;

use Carbon\Carbon;
use App\Models\Entry;
use Illuminate\Support\Facades\DB;

class EntriesMonthlyBalance
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        // TODO implement the resolver
        $now = Carbon::now();

        return Entry::whereMonth(DB::raw('STR_TO_DATE(date, "%d/%m/%Y")'), $now->month)
            ->whereYear(DB::raw('STR_TO_DATE(date, "%d/%m/%Y")'), $now->year)
            ->where('value', '>', '0')
            ->where('is_canceled', false)
            ->sum('value');
    }
}
