<?php

namespace App\GraphQL\Queries;

use App\Util\Helper;
use App\Models\AppConfig;
use App\GraphQL\Traits\ConfigTrait;
use Illuminate\Support\Facades\Validator;

class ConfigGet
{
    use ConfigTrait;

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $data = $this->getFormattedData($args);

        $this->validator($data)->validate();

        $config = AppConfig::where('name', $data['name'])->first();
        $decodedConfig = collect(json_decode($config->json));

        if (!Helper::filled($data, 'key')) {
            return $decodedConfig ?? null;
        }

        return $decodedConfig[$data['key']] ?? null;
    }
}
