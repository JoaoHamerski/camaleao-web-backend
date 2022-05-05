<?php

namespace App\GraphQL\Mutations;

use App\Models\DailyCashReminder;
use Illuminate\Support\Facades\Validator;

class DailyCashReminderDelete
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'id' => ['required', 'exists:daily_cash_reminders,id']
        ])->validate();

        $reminder = DailyCashReminder::find($args['id']);

        $reminder->delete();

        return $reminder;
    }
}
