<?php

namespace App\GraphQL\Mutations;

use App\Models\City;
use Illuminate\Support\Facades\Validator;

class CityCreate
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'name' => ['required', 'max:191'],
            'state_id' => ['required', 'exists:states,id']
        ], $this->errorMessages())->validate();

        return City::create($args);
    }

    public function errorMessages()
    {
        return [
            'state_id.required' => __('general.validation.state_id_required')
        ];
    }
}
