<?php

namespace App\GraphQL\Mutations;

use App\Models\Branch;
use Illuminate\Support\Facades\Validator;

class BranchDelete
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'id' => ['required', 'exists:branches,id']
        ])->validate();

        $branch = Branch::find($args['id']);
        $branch->delete();

        return $branch;
    }
}
