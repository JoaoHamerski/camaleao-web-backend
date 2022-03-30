<?php

namespace App\GraphQL\Mutations;

use Error;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class Login
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $guard = Auth::guard(config('sanctum.guard', 'web'));

        $this->validator($args)->validate();

        if (!$guard->attempt($args, $args['remember'] ?? false)) {
            throw new Error('Invalid credentials.');
        }

        $user = $guard->user();

        return [
            'token' => $user->createToken('default')->plainTextToken
        ];
    }

    public function validator($data)
    {
        return Validator::make($data, [
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);
    }
}
