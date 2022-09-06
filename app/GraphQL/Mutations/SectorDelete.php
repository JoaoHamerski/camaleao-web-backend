<?php

namespace App\GraphQL\Mutations;

use App\Models\Sector;
use Illuminate\Support\Facades\Validator;

class SectorDelete
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'id' => ['required', 'exists:sectors,id']
        ])->validate();

        $sector = Sector::find($args['id']);
        $sector->delete();

        return $sector;
    }
}
