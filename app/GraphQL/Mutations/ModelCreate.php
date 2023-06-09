<?php

namespace App\GraphQL\Mutations;

use App\Models\Model;
use Illuminate\Support\Facades\Validator;

class ModelCreate
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

        return Model::create($args);
    }
}
