<?php

namespace App\GraphQL\Queries;

use App\Models\Entry;
use App\Models\Via;

class EntriesBankMirrorNavs
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $vias = Via::all();

        return $vias->map(fn ($via) => [
            'via' => $via,
            'count' => Entry::where('via_id', $via->id)->count()
        ]);
    }
}
