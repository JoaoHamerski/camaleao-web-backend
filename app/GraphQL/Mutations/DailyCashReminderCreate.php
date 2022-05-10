<?php

namespace App\GraphQL\Mutations;

use App\Models\DailyCashReminder;
use App\Util\Formatter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DailyCashReminderCreate
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $data = $this->getFormattedData($args);
        $this->validator($data)->validate();

        $reminder = Auth::user()->dailyCashReminders()->create($data);

        return $reminder;
    }

    public function getFormattedData(array $data)
    {
        return (new Formatter($data))
            ->date('date')
            ->currencyBRL('value')
            ->get();
    }

    public function validator(array $data)
    {
        return Validator::make($data, [
            'description' => ['nullable', 'max:191'],
            'date' => ['required', 'date'],
            'value' => ['required'],
            'type' => ['required', Rule::in(['expense', 'payment'])]
        ], $this->errorMessages());
    }

    public function errorMessages()
    {
        return [
            'value.required' => __('validation.rules.required'),
            'date.required' => __('validation.rules.required'),
            'date.date' => __('validation.rules.date')
        ];
    }
}
