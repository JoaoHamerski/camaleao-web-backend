<?php

namespace App\GraphQL\Builders;

use Illuminate\Database\Eloquent\Builder;

class NotesOrderBuilder
{
    public function __invoke(Builder $builder)
    {
        return $builder->whereNull('is_reminder');
    }
}
