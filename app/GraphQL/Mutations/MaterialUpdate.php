<?php

namespace App\GraphQL\Mutations;

use App\Models\Material;
use Illuminate\Support\Facades\Validator;

final class MaterialUpdate
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'id' => ['required', 'exists:materials'],
            'name' => ['required']
        ])->validate();

        $material = Material::find($args['id']);
        $material->update($args);

        return $material;
    }
}
