<?php

namespace App\GraphQL\Mutations;

use App\Models\SleeveType;
use Illuminate\Support\Facades\Validator;

class SleeveTypeCreate
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

        return SleeveType::create($args);
    }
}
