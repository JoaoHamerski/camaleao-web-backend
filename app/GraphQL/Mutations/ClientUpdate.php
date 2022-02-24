<?php

namespace App\GraphQL\Mutations;

use App\GraphQL\Traits\ClientTrait;
use App\Models\Client;

class ClientUpdate
{
    use ClientTrait;

    const IS_UPDATE = true;

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $data = $this->getFormattedData($args);

        $this->validator($data, self::IS_UPDATE)->validate();

        $client = Client::find($data['id']);
        $client->update($data);

        return $client;
    }
}
