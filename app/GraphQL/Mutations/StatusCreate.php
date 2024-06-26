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

        $lastStatus = Status::ordered()->get()->last();
        $status = Status::create([
            'text' => $args['text'],
            'order' => $lastStatus ? $lastStatus->order + 1 : 1
        ]);

        return $status;
    }
}
