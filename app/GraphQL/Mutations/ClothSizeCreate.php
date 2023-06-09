<?php

namespace App\GraphQL\Mutations;

use App\Models\ClothSize;
use Illuminate\Support\Facades\Validator;

class ClothSizeCreate
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

        return ClothSize::create($args);
    }
}
