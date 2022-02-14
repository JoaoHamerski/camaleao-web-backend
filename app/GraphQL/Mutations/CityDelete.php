<?php

namespace App\GraphQL\Mutations;

use App\Models\Branch;
use App\Models\City;
use App\Util\Helper;
use App\Models\Client;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class CityDelete
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'id' => ['required', 'exists:cities,id'],
            'replace.*' => ['nullable', 'boolean'],
            'city_id' => [
                'nullable',
                'required_if:replace.clients,true',
                'required_if:replace.branches,true',
                'exists:cities,id'
            ]
        ], $this->errorMessages())->validate();

        $city = City::find($args['id']);

        if (data_get($args, 'replace.clients', false)) {
            Client::where('city_id', '=', $city->id)
                ->update(['city_id' => $args['city_id']]);
        }

        if (data_get($args, 'replace.branches', false)) {
            Branch::where('city_id', '=', $city->id)
                ->update(['city_id' => $args['city_id']]);
        }

        $city->delete();

        return $city;
    }

    public function errorMessages()
    {
        return [
            'city_id.required_if' => __('general.validation.cities.city_id_required_if')
        ];
    }
}
