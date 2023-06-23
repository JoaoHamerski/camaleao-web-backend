<?php

namespace App\GraphQL\Mutations;

use App\GraphQL\Traits\GarmentMatchTrait;
use App\Models\GarmentMatch;
use Illuminate\Support\Arr;

class GarmentMatchCreate
{
    use GarmentMatchTrait;
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $input = $this->getFormattedInput($args);
        $this->validator($input)->validate();

        $match = GarmentMatch::create(Arr::only($input, [
            'model_id',
            'material_id',
            'neck_type_id',
            'sleeve_type_id',
            'unique_value'
        ]));

        if (!$input['is_unique_value']) {
            $match->values()->createMany($input['values']);
        }

        $match->sizes()->attach($input['sizes']);

        return $match;
    }
}
