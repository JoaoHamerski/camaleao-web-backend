<?php

namespace App\GraphQL\Builders;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class SectorOrdersByPeriodBuilder
{
    public function __invoke($root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        return $root['current_orders'];
    }
}
