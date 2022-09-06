<?php

namespace App\GraphQL\Mutations;

use Exception;
use Illuminate\Support\Facades\Artisan;

class StatusSync
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        try {
            Artisan::call('sync:status');
            return true;
        } catch (Exception $e) {
            return false;
        }

        return true;
    }
}
