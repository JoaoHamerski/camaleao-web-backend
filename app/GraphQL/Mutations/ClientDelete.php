<?php

namespace App\GraphQL\Mutations;

use App\Models\Client;
use Illuminate\Support\Facades\Validator;

class ClientDelete
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'id' => ['required', 'exists:clients,id']
        ])->validate();

        $client = Client::find($args['id']);
        $client->delete();

        return $client;
    }
}
