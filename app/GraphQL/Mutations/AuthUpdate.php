<?php

namespace App\GraphQL\Mutations;

use App\Util\Formatter;
use App\Util\Helper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthUpdate
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $data = $this->getFormattedData($args);
        $this->validator($data)->validate();

        $data = $this->getFormattedPassword($data);

        Auth::user()->update($data);

        return Auth::user();
    }

    public function getFormattedData($data)
    {
        return (new Formatter($data))
            ->name('name')
            ->get();
    }

    public function getFormattedPassword($data)
    {
        if (Helper::filled($data, 'password')) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        return $data;
    }

    public function validator($data)
    {
        return Validator::make($data, [
            'name' => ['sometimes', 'required', 'max:191'],
            'email' => ['sometimes', 'required', 'email', 'max:191'],
            'password' => ['nullable', 'min:5', 'confirmed'],
            'password_confirmation' => ['nullable', 'required_with:password']
        ]);
    }
}
