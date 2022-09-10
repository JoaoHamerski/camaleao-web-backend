<?php

namespace App\GraphQL\Queries;

use App\Models\Sector;

class Sectors
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        return Sector::ordered();
    }
}
