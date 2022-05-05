<?php

namespace App\GraphQL\Mutations;

use App\Util\Formatter;
use Illuminate\Validation\Rule;
use App\Models\DailyCashReminder;
use Illuminate\Support\Facades\Validator;

class DailyCashReminderUpdate
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $data = $this->getFormattedData($args);
        $this->validator($data)->validate();

        $reminder = DailyCashReminder::find($data['id']);

        $reminder->update($data);

        return $reminder;
    }

    public function validator($data)
    {
        return Validator::make($data, [
            'id' => ['required', 'exists:daily_cash_reminders,id'],
            'description' => ['nullable', 'max:191'],
            'date' => ['nullable', 'date'],
            'value' => ['nullable'],
            'type' => ['nullable', Rule::in(['expense', 'payment'])]
        ]);
    }

    public function getFormattedData(array $data)
    {
        return (new Formatter($data))
            ->date('date')
            ->currencyBRL('value')
            ->get();
    }
}
