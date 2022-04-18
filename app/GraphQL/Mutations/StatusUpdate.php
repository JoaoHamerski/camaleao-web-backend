<?php

namespace App\GraphQL\Mutations;

use App\Models\Status;
use App\GraphQL\Traits\StatusTrait;

class StatusUpdate
{
    use StatusTrait;

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $this->validator($args, $isUpdate = true)->validate();

        $status = Status::find($args['id']);
        $status->update([
            'text' => $args['text']
        ]);

        return $status;
    }
}
