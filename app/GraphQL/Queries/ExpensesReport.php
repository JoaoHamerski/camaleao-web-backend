<?php

namespace App\GraphQL\Queries;

use App\Util\Mask;
use App\Util\Formatter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class ExpensesReport
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $data = $this->getFormattedData($args);
        $this->validator($data)->validate();

        return URL::temporarySignedRoute(
            'pdf.expenses-report',
            now()->addSeconds(60),
            $data
        );
    }

    public function getFormattedData($data)
    {
        return (new Formatter($data))
            ->date(['start_date', 'final_date'])
            ->get();
    }

    public function validator($data)
    {
        return Validator::make($data, [
            'start_date' => ['required', 'date'],
            'final_date' => [
                'nullable',
                'date',
                $data['start_date']
                    ? 'after:' . $data['start_date']
                    : ''
            ]
        ], $this->errorMessages());
    }

    public function errorMessages()
    {
        return [
            'start_date.required' => __('general.validation.date_required'),
            'start_date.date' => __('general.validation.date_invalid'),
            'final_date.date' => __('general.validation.date_invalid'),
            'final_date.after' => __('general.validation.date_after', [
                'date' => 'data inicial'
            ])
        ];
    }
}
