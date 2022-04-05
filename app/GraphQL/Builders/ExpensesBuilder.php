<?php

namespace App\GraphQL\Builders;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class ExpensesBuilder
{
    public function __invoke(Builder $builder)
    {
        if (Auth::user()->hasRole('gerencia')) {
            return $builder;
        }

        return $builder->where('user_id', '=', Auth::id());
    }
}
