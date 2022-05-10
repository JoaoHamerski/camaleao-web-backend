<?php

namespace App\GraphQL\Mutations;

use App\Models\City;
use App\Models\State;
use Illuminate\Support\Facades\Validator;

class CityMassUpdateState
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'exists:cities,id'],
            'state_id' => ['required', 'exists:states,id']
        ], $this->errorMessages())->validate();

        $state = State::find($args['state_id']);

        $cities = City::whereIn('id', $args['ids']);
        $cities->update([
            'state_id' => $state->id
        ]);

        return $cities->get();
    }

    public function errorMessages()
    {
        return [
            'state_id.required' => __('validation.rules.required_list', ['pronoun' => 'um'])
        ];
    }
}
