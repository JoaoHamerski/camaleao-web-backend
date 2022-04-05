<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UserDelete
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'id' => ['required', 'exists:users,id'],
            'auth_password' => ['required', 'current_password']
        ], $this->errorMessages())->validate();

        $user = User::find($args['id']);
        $user->delete();

        return $user;
    }

    public function errorMessages()
    {
        return [
            'auth_password.required' => __('general.validation.password_required'),
            'auth_password.current_password' => __('general.validation.current_password')
        ];
    }
}
