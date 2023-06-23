<?php

namespace App\GraphQL\Mutations;

use App\Models\NeckType;
use Illuminate\Support\Facades\Validator;

final class NeckTypeUpdate
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'id' => ['required', 'exists:neck_types'],
            'name' => ['required']
        ])->validate();

        $neckType = NeckType::find($args['id']);
        $neckType->update($args);

        return $neckType;
    }
}
