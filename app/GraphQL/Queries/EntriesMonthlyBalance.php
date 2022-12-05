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
        $now = Carbon::now();
        $previous = $now->clone()->subMonth();

        return [
            'current' => $this->getBalance($now),
            'previous' => $this->getBalance($previous)
        ];
    }

    private function getBalance(Carbon $date)
    {
        return Entry::whereMonth(DB::raw('STR_TO_DATE(date, "%d/%m/%Y")'), $date->month)
            ->whereYear(DB::raw('STR_TO_DATE(date, "%d/%m/%Y")'), $date->year)
            ->where('value', '>', '0')
            ->where('is_canceled', false)
            ->sum('value');
    }
}
