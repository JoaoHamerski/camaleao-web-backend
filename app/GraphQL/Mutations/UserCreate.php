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
            'name.required' => __('validation.rules.required'),
            'email.required' => __('validation.rules.required'),
            'email.email' => __('validation.rules.email'),
            'password.required' => __('validation.rules.required'),
            'password.confirmed' => __('validation.rules.password_confirmed'),
            'role_id.required' => __('validation.rules.required_list', [
                'pronoun' => 'um',
                'attribute' => 'privilégio'
            ])
        ];
    }

    public function getFormattedData($data)
    {
        return (new Formatter($data))
            ->name('name')
            ->get();
    }
}
