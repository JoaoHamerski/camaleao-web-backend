<?php

namespace App\GraphQL\Mutations;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthDelete
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'password' => ['required', 'current_password']
        ], $this->errorMessages())->validate();

        $authUser = Auth::user();
        $authUser->delete();

        return $authUser;
    }

    public function errorMessages()
    {
        return [
            'password.current_password' => __('general.validation.current_password')
        ];
    }
}
