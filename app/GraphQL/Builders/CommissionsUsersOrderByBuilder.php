<?php

namespace App\GraphQL\Builders;

use Illuminate\Database\Eloquent\Builder;

class CommissionsUsersOrderByBuilder
{
    public function __invoke(Builder $builder)
    {
        return $builder
            ->join(
                'commissions',
                'commission_user.commission_id',
                '=',
                'commissions.id'
            )
            ->join(
                'users',
                'commission_user.user_id',
                '=',
                'users.id'
            )
            ->orderBy('commissions.created_at', 'desc')
            ->select('commission_user.*');
    }
}
