<?php

namespace App\GraphQL\Mutations;

use App\Models\GarmentSize;
use Illuminate\Support\Facades\Validator;

final class GarmentSizeUpdate
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'id' => ['required', 'exists:garment_sizes'],
            'name' => ['required']
        ])->validate();

        $garmentSize = GarmentSize::find($args['id']);
        $garmentSize->update($args);

        return $garmentSize;
    }
}
