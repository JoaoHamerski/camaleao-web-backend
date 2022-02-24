<?php

namespace App\GraphQL\Mutations;

use App\GraphQL\Traits\BranchTrait;
use App\Models\Branch;
use App\Models\City;

class BranchUpdate
{
    use BranchTrait;

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $this->validator($args)->validate();

        $branch = Branch::find($args['id']);
        $branch->update($args);

        City::whereIn('id', $branch->cities->pluck('id'))
            ->update([
                'branch_id' => null
            ]);

        City::whereIn('id', $args['cities_id'])->update([
            'branch_id' => $branch->id
        ]);

        return $branch;
    }
}
