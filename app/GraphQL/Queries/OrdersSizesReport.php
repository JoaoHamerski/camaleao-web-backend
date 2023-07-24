<?php

namespace App\GraphQL\Queries;

use App\Util\Formatter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

final class OrdersSizesReport
{
    protected static $VALID_FIELDS = ['model', 'material', 'neck_type', 'sleeve_type'];

    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        $input = $this->getFormattedData($args);

        static::validator($input)->validate();

        return URL::temporarySignedRoute(
            'pdf.orders-sizes',
            now()->addMinutes(1000),
            $input
        );
    }

    public static function validator($data)
    {
        return Validator::make($data, [
            'initial_date' => ['required', 'date'],
            'final_date' => ['nullable', 'date'],
            'groups' => ['array', 'required'],
            'groups.*' => [Rule::in(static::$VALID_FIELDS)],
            'indicators' => ['required', 'boolean'],
            'start_status_to_ignore' => ['required', 'exists:status,id']
        ], static::errorMessages());
    }

    public static function errorMessages()
    {
        return [
            'initial_date.required' => __('validation.rules.required'),
            'initial_date.date' => __('validation.rules.date'),
            'final_date.date' => __('validation.rules.date'),
            'groups.required' => 'Você deve selecionar ao menos um grupo',
            'groups.*.in' => 'O grupo seleciona é inválido.'
        ];
    }

    public function getFormattedData(array $data)
    {
        return (new Formatter($data))
            ->date(['initial_date', 'final_date'])
            ->get();
    }
}
