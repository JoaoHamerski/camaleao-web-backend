<?php

namespace App\GraphQL\Mutations;

use App\Models\AppConfig;
use App\Models\Status;
use Illuminate\Support\Facades\Validator;

class StatusSetAvailable
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'ids.*' => ['nullable', 'exists:status,id']
        ])->validate();

        AppConfig::set('app', 'status_available', $args['ids']);

        $status = Status::whereIn('id', $args['ids']);

        return $status->get();
    }
}
