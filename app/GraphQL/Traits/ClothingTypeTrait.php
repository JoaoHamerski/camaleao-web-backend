<?php

namespace App\GraphQL\Traits;

use App\Util\Helper;
use App\Util\Formatter;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

trait ClothingTypeTrait
{
    public function getFormattedData($data)
    {
        if (Helper::filled($data, 'name')) {
            $data['key'] = Str::slug($data['name']);
        }

        return (new Formatter($data))
            ->capitalize('name')
            ->get();
    }

    public function getRequiredRule($isUpdate)
    {
        return $isUpdate ? 'nullable' : 'required';
    }

    public function getUniqueRule($idToIgnore = null)
    {
        return $idToIgnore
            ? Rule::unique('clothing_types')->ignore($idToIgnore)
            : Rule::unique('clothing_types');
    }

    public function validator($data, $isUpdate = false)
    {
        return Validator::make($data, [
            'id' => ['sometimes', 'required', 'exists:clothing_types,id'],
            'name' => [$this->getRequiredRule($isUpdate), 'max:191'],
            'is_hidden' =>  [$this->getRequiredRule($isUpdate), 'boolean'],
            'key' => [$this->getRequiredRule($isUpdate), $this->getUniqueRule($data['id'] ?? null)]
        ], $this->errorMessages());
    }

    public function errorMessages()
    {
        return [
            'name.required' => __('validation.rules.required'),
            'key.unique' => __('validation.custom.clothing_types.key|unique')
        ];
    }
}
