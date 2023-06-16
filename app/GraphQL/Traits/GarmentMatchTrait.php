<?php

namespace App\GraphQL\Traits;

use App\Models\GarmentMatch;
use App\Util\Formatter;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

trait GarmentMatchTrait
{
    public function formatSizes($input)
    {
        $sizes = collect($input['sizes']);
        $sizes = $sizes->filter(fn ($size) => $size['is_shown']);
        $sizes = $sizes->map(fn ($size) => [
            'garment_size_id' => $size['id'],
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

    public function uniqueMatchRule($input, $match)
    {
        return Rule::unique('garment_matches')->where(
            fn ($query) => $query->where('model_id', $input['model_id'])
                ->where('material_id', $input['material_id'])
                ->where('neck_type_id', $input['neck_type_id'])
                ->where('sleeve_type_id', $input['sleeve_type_id'])
                ->where('deleted_at', null)
        )->ignore($match);
    }

    public function getGeneralRules($input, $match = null)
    {
        $rules[] = [];
        $rules[] = [
            'is_unique_value' => ['required', 'boolean'],
            'model_id' => ['required', 'exists:models,id', $this->uniqueMatchRule($input, $match)],
            'material_id' => ['nullable', 'exists:materials,id'],
            'neck_type_id' => ['nullable', 'exists:neck_types,id'],
            'sleeve_type_id' => ['nullable', 'exists:sleeve_types,id'],
            'sizes' => ['required', 'array'],
            'sizes.*.garment_size_id' => ['required', 'exists:garment_sizes,id'],
            'sizes.*.is_shown' => ['nullable', 'boolean'],
        ];

        if ($match) {
            $rules[] = ['id' => ['required', 'exists:garment_matches']];
        }

        return Arr::collapse($rules);
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

    public function getRules(array $input, $match = null)
    {

        $rules = [];
        $rules[] = $this->getGeneralRules($input, $match);
        $rules[] = $input['is_unique_value']
            ? $this->getRulesIfUniqueValue($input)
            : $this->getRulesIfNotUniqueValue($input);

        return Arr::collapse($rules);
    }

    public function validator(array $input, $match = null)
    {
        if (isset($input['id']) && !$match) {
            $match = GarmentMatch::find($input['id']);
        }

        $rules = $this->getRules($input, $match);

        return Validator::make($input, $rules, $this->getErrorMessages());
    }

    public function getErrorMessages()
    {
        return [
            'model_id.required' => __('validation.rules.required_list'),
            'model_id.unique' => 'unique',
            'values.*.end.required' => '',
            'values.*.value.required' => __('validation.rules.required', ['attribute' => 'valor']),
            'unique_value.required' => __('validation.rules.required', ['attribute' => 'valor único'])
        ];
    }
}
