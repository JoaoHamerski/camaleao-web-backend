<?php

namespace App\GraphQL\Handlers;

use App\Util\Formatter;
use \Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Nuwave\Lighthouse\WhereConditions\WhereConditionsHandler;

class WhereCashFlowHandler extends WhereConditionsHandler
{
    public function __invoke(
        object $builder,
        array $whereConditions,
        ?Model $model = null,
        string $boolean = 'and'
    ): void {
        if ($this->isQueringAnyDate($whereConditions)) {
            $whereConditions = $this->getFormattedData($whereConditions);
            $this->validator($whereConditions)->validate();
        }

        if ($this->isQueringBetweenDates($whereConditions)) {
            @list($start, $final) = $whereConditions['value'];

            $builder->whereBetween('date', [$start, $final]);

            return;
        }

        parent::__invoke($builder, $whereConditions, $model, $boolean);
    }

    /**
     * Método chamado apenas quando a clause "where" tem como
     * coluna uma data, assim formatando para ISO/SQL os valores
     * passados
     *
     * @param array $data
     * @return array
     */
    public function getFormattedData($data)
    {
        $fieldToFormat = is_array($data['value'])
            ? 'value.*'
            : 'value';

        return (new Formatter($data))
            ->date($fieldToFormat)
            ->get();
    }

    /**
     * Pequena validação necessária caso seja feito um filtro entre datas
     *
     * @param array $data
     * @return Illuminate\Support\Facades\Validator;
     */
    public function validator($data)
    {
        $newData = [];

        if (!$this->isQueringBetweenDates($data)) {
            $newData = ['start_date' => $data['value']];
        } else {
            $newData = [
                'start_date' => $data['value'][0],
                'final_date' => $data['value'][1],
            ];
        }

        return Validator::make($newData, [
            'start_date' => ['required', 'date'],
            'final_date' => [
                'nullable',
                'date',
                'after:start_date'
            ]
        ], $this->errorMessages());
    }

    public function errorMessages()
    {
        return [
            'start_date.required' => __('general.validation.date_required'),
            'start_date.date' => __('general.validation.date_valid'),
            'final_date.date' => __('general.validation.date_valid'),
            'final_date.after' => __('general.validation.date_after')
        ];
    }

    public function isQueringAnyDate($data)
    {
        return isset($data['column'])
            && $data['column'] === 'date';
    }

    public function isQueringBetweenDates($data)
    {
        return isset($data['column'])
            && $data['column'] === 'date'
            && strtolower($data['operator']) === 'between';
    }
}
