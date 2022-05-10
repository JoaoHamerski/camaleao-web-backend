<?php

namespace App\GraphQL\Mutations;

use App\GraphQL\Traits\CityTrait;
use App\Models\City;
use Illuminate\Support\Facades\Validator;

class CityUpdate
{
    use CityTrait;

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'id' => ['required', 'exists:cities,id'],
            'name' => ['sometimes', 'required', 'max:191'],
            'state_id' => ['sometimes', 'required', 'exists:states,id']
        ], $this->errorMessages())->validate();

        $city = City::find($args['id']);
        $city->update($args);

        return $city;
    }
}
