<?php

namespace App\GraphQL\Mutations;

use App\Models\AppConfig;
use Illuminate\Support\Facades\Validator;
use App\GraphQL\Traits\ConfigTrait;
use Illuminate\Validation\Rule;

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

        $config = AppConfig::where('name', $data['name'])->first();

        $configDecoded = [$data['key'] => $data['value']];

        if ($config) {
            $configDecoded = collect(json_decode($config->json));
            $configDecoded = $configDecoded->merge([$data['key'] => $data['value']]);
        }

        $config = AppConfig::updateOrCreate(
            ['name' => $data['name']],
            ['json' => json_encode($configDecoded)]
        );

        return $config;
    }

    public function validator($data)
    {
        return Validator::make($data, [
            'name' => ['required'],
            'key' => ['required'],
            'value' => ['nullable']
        ]);
    }
}
