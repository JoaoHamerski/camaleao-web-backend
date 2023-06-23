<?php

namespace App\GraphQL\Mutations;

use App\Models\GarmentSize;
use Illuminate\Support\Facades\Validator;

class GarmentSizeCreate
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'name' => ['required', 'string', 'max:2']
        ])->validate();

        return GarmentSize::create($args);
    }
}
