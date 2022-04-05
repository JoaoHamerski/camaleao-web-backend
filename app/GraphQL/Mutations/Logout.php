<?php

namespace App\GraphQL\Mutations;

use Illuminate\Support\Facades\Auth;

class Logout
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $guard = Auth::guard(config('sanctum.guard', 'web'));
        $user = request()->user();

        $user->currentAccessToken()->delete();

        $guard->logout();

        return $user;
    }
}
