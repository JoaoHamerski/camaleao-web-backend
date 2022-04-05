<?php

namespace App\GraphQL\Traits;

use App\Util\Formatter;
use Illuminate\Support\Facades\Validator;

trait ConfigTrait
{
    public function getFormattedData($data)
    {
        return (new Formatter($data))
            ->snake('key')
            ->get();
    }
}
