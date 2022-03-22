<?php

namespace App\GraphQL\Mutations;

use App\Models\Client;
use App\GraphQL\Traits\ClientTrait;

class ClientCreate
{
    use ClientTrait;

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $data = $this->getFormattedData($args);

        $this->validator($data)->validate();

        return Client::create($data);
    }
}
