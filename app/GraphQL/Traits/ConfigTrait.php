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

    public function getUniqueOrExistsRule($isNew)
    {
        return $isNew
            ? 'unique:configs,name'
            : 'exists:configs,name';
    }

    public function validator($data, $isNew = false)
    {
        return Validator::make($data, [
            'name' => ['required', $this->getUniqueOrExistsRule($isNew)],
            'key' => ['nullable']
        ]);
    }
}
