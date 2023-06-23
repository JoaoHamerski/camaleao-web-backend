<?php

namespace App\GraphQL\Mutations;

use App\Models\GarmentMatch;
use Illuminate\Support\Facades\Validator;

final class GarmentMatchDelete
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, ['id' => ['required', 'exists:garment_matches']]);

        $match = GarmentMatch::find($args['id']);
        $match->delete();

        return $match;
    }
}
