<?php

namespace App\GraphQL\Mutations;

use App\Models\Material;
use Illuminate\Support\Facades\Validator;

class MaterialCreate
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'name' => ['required']
        ])->validate();

        return Material::create($args);
    }
}
