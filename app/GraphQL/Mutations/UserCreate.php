<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
use App\Util\Formatter;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserCreate
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $data = $this->getFormattedData($args);
        $this->validator($data)->validate($data);

        $data['password'] = Hash::make($data['password']);

        return User::create($data);
    }

    public function validator($data)
    {
        return Validator::make($data, [
            'name' => ['required', 'max:191'],
            'email' => ['required', 'max:191', 'email'],
            'password' => ['required', 'min:5', 'confirmed'],
            'password_confirmation' => ['nullable'],
            'role_id' => ['required', 'exists:roles,id']
        ], $this->errorMessages());
    }

    public function errorMessages()
    {
        return [
            'email.email' => __('general.validation.email')
        ];
    }

    public function getFormattedData($data)
    {
        return (new Formatter($data))
            ->name('name')
            ->get();
    }
}
