<?php

namespace App\GraphQL\Builders;

use Illuminate\Database\Eloquent\Builder;

class CommissionsUsersOrderByBuilder
{
    public function __invoke(Builder $builder)
    {
        return $builder->join(
            'commissions',
            'commission_user.commission_id',
            '=',
            'commissions.id'
        )->orderBy('commissions.created_at', 'desc');
    }
}
