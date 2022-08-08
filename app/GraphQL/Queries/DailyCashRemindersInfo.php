<?php

namespace App\GraphQL\Queries;

use App\Models\DailyCashReminder;
use Carbon\Carbon;

class DailyCashRemindersInfo
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        return [
            'has_near_reminder' => $this->getHasNearReminder(),
            'has_expired_reminder' => $this->getHasExpiredReminder()
        ];
    }


    public function getHasExpiredReminder()
    {
        $today = Carbon::now();

        return DailyCashReminder::where(
            'date',
            '<',
            $today->toDateString()
        )->exists();
    }

    public function getHasNearReminder()
    {
        $today = Carbon::now();
        $betweenDates = [
            $today->clone()->toDateString(),
            $today->addDays(5)->toDateString()
        ];

        return DailyCashReminder::whereBetween(
            'date',
            $betweenDates
        )->exists();
    }
}
