<?php

namespace App\GraphQL\Mutations;

use App\Models\Status;
use App\GraphQL\Traits\StatusTrait;

class StatusCreate
{
    use StatusTrait;

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $this->validator($args)->validate();

        $status = Status::create([
            'text' => $args['text'],
            'order' => Status::ordered()->get()->last()->order + 1
        ]);

        return $status;
    }
}
