<?php

namespace App\GraphQL\Builders;

use App\Models\Entry;
use Illuminate\Support\Facades\DB;

class EntriesBankMirrorBuilder
{
    public function __invoke()
    {
        return Entry::select(['*', DB::raw('STR_TO_DATE(date, "%d/%m/%Y") AS date')]);
    }
}
