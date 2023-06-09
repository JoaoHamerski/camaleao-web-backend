<?php

namespace App\GraphQL\Mutations;

use App\Models\NeckType;
use Illuminate\Support\Facades\Validator;

class NeckTypeCreate
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

        return NeckType::create($args);
    }
}
