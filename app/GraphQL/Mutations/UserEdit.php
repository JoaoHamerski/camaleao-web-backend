<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserEdit
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'id' => ['required', 'exists:users,id'],
            'role' => ['required', 'exists:roles,id'],
            'password' => ['nullable', 'min:6']
        ])->validate();

        $user = User::find($args['id']);
        $user->role_id = $args['role'];

        if (!empty($args['password'])) {
            $user->password = Hash::make($args['password']);
        }

        $user->save();

        return $user;
    }
}
