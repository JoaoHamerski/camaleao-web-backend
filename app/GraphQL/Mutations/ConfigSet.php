<?php

namespace App\GraphQL\Mutations;

use App\Models\Config;
use Illuminate\Support\Facades\Validator;
use App\GraphQL\Traits\ConfigTrait;

class ConfigSet
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

        $config = Config::where('name', $data['name'])->first();

        $configDecoded = collect(json_decode($config->json));
        $configDecoded = $configDecoded->merge([$data['key'] => $data['value']]);

        $config->update(['json' => $configDecoded->toJson()]);

        return $config;
    }
}
