<?php

namespace App\GraphQL\Mutations;

use App\Models\ClothMatch;
use App\Util\Formatter;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ClothMatchCreate
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $input = $this->getFormattedInput($args);
        $this->validator($input)->validate();

        $clothMatch = ClothMatch::create(Arr::only($input, [
            'model_id',
            'material_id',
            'neck_type_id',
            'sleeve_type_id',
            'unique_value'
        ]));

        if (!$input['is_unique_value']) {
            $clothMatch->values()->createMany($input['values']);
        }

        $clothMatch->sizes()->attach($input['sizes']);

        return $clothMatch;
    }

    public function formatSizes($input)
    {
        $sizes = collect($input['sizes']);
        $sizes = $sizes->filter(fn ($size) => $size['is_shown']);
        $sizes = $sizes->map(fn ($size) => [
            'cloth_size_id' => $size['id'],
            'value' => $size['value']
        ]);

        $input['sizes'] = $sizes->toArray();

        return $input;
    }

    public function formatValues($input)
    {
        $values = $input['values'];
        $valuesLength = count($input['values']);

        foreach ($values as $index => $value) {
            if ($index >= $valuesLength - 1) {
                continue;
            }

            $values[$index + 1]['start'] = $value['end'] + 1;
        }

        $input['values'] = $values;

        return $input;
    }

    public function getFormattedInput($args)
    {
        $formatted = (new Formatter($args))
            ->currencyBRL([
                'sizes.*.value',
                'values.*.value',
                'unique_value'
            ])
            ->get();

        $formatted = $this->formatValues($formatted);
        $formatted = $this->formatSizes($formatted);

        if (!$formatted['is_unique_value']) {
            unset($formatted['unique_value']);
        }

        return $formatted;
    }

    public function getGeneralRules($input)
    {
        return [
            'is_unique_value' => ['required', 'boolean'],
            'model_id' => ['required', 'exists:models,id'],
            'material_id' => ['nullable', 'exists:materials,id'],
            'neck_type_id' => ['nullable', 'exists:neck_types,id'],
            'sleeve_type_id' => ['nullable', 'exists:sleeve_types,id'],
            'sizes' => ['required', 'array'],
            'sizes.*.cloth_size_id' => ['required', 'exists:cloth_sizes,id'],
            'sizes.*.is_shown' => ['nullable', 'boolean'],
        ];
    }

    public function getRulesIfUniqueValue($input)
    {
        return [
            'unique_value' => ['required', 'min:0']
        ];
    }

    public function getRulesIfNotUniqueValue($input)
    {
        $lastIndexOfValues = count($input['values']) - 1;

        return [
            'values' => ['required', 'array'],
            'values.0.start' => ['required', Rule::in(['0'])],
            'values.*.start' => ['required', 'integer'],
            'values.*.end' => ['required', 'integer'],
            "values.$lastIndexOfValues.start" => ['nullable'],
            "values.$lastIndexOfValues.end" => ['nullable'],
            'values.*.value' => ['required', 'min:0']
        ];
    }

    public function getRules(array $input)
    {
        $rules = [];
        $rules[] = $this->getGeneralRules($input);
        $rules[] = $input['is_unique_value']
            ? $this->getRulesIfUniqueValue($input)
            : $this->getRulesIfNotUniqueValue($input);

        return Arr::collapse($rules);
    }

    public function validator(array $input)
    {
        $rules = $this->getRules($input);

        return Validator::make($input, $rules, $this->getErrorMessages());
    }

    public function getErrorMessages()
    {
        return [
            'model_id.required' => __('validation.rules.required_list'),
            'values.*.end.required' => '',
            'values.*.value.required' => __('validation.rules.required', ['attribute' => 'valor']),
            'unique_value.required' => __('validation.rules.required', ['attribute' => 'valor único'])
        ];
    }
}
