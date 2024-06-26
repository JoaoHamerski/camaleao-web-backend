<?php

namespace App\GraphQL\Mutations;

use App\Models\AppConfig;
use App\GraphQL\Traits\ConfigTrait;
use Illuminate\Support\Facades\Validator;

class ConfigRemove
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

        $configDecoded = collect(json_decode($config->json));
        $configDecoded->forget($data['key']);

        $config->update(['json' => $configDecoded->toJson()]);

        return $config;
    }

    public function validator($data)
    {
        return Validator::make($data, [
            'name' => ['required', 'exists:configs,name'],
            'key' => ['required']
        ]);
    }
}
