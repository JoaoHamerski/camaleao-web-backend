<?php

namespace App\GraphQL\Mutations;

use App\Models\SleeveType;
use Illuminate\Support\Facades\Validator;

final class SleeveTypeUpdate
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'id' => ['required', 'exists:sleeve_types'],
            'name' => ['required']
        ])->validate();

        $sleeveType = SleeveType::find($args['id']);
        $sleeveType->update($args);

        return $sleeveType;
    }
}
