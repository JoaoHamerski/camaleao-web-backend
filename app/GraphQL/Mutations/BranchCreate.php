<?php

namespace App\GraphQL\Mutations;

use App\Models\Branch;
use App\Models\City;
use App\GraphQL\Traits\BranchTrait;

class BranchCreate
{
    use BranchTrait;

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $this->validator($args)->validate();

        $branch = Branch::create($args);

        City::whereIn('id', $args['cities_id'])->update([
            'branch_id' => $branch->id
        ]);

        return $branch;
    }
}
