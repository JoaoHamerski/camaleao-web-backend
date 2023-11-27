<?php

namespace App\GraphQL\Builders;

use App\Models\Client;
use Illuminate\Support\Facades\Validator;

class BonusesFromClientBuilder
{
    public function __invoke($_, $args)
    {
        Validator::make($args, [
            'id' => ['required', 'exists:clients']
        ])->validate();

        $client = Client::find($args['id']);

        return $client->bonuses();
    }
}
