<?php

namespace App\GraphQL\Builders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class PaymentBuilder
{
    public function __invoke(Builder $builder)
    {
        $pendencies = $builder->pendencies()
            ->groupBy('created_at')
            ->orderBy(DB::raw('DATE(created_at)'), 'desc');

        return $pendencies;
    }
}
