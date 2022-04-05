<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UserChangeRole
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'id' => ['required', 'exists:users,id'],
            'new_role_id' => ['required', 'exists:roles,id']
        ])->validate();

        $user = User::find($args['id']);

        $user->update(['role_id' => $args['new_role_id']]);

        return $user;
    }
}
