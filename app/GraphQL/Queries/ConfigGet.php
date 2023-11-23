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
        $decodedConfig = collect(json_decode($config->json, true));

        if (!Helper::filled($data, 'key')) {
            if (!$decodedConfig) {
                return null;
            }

            return $args['encoded']
                ? json_encode($decodedConfig)
                : $decodedConfig;
        }

        if (!isset($decodedConfig[$data['key']])) {
            return null;
        }

        return $args['encoded']
            ? json_encode($decodedConfig[$data['key']])
            : $decodedConfig[$data['key']];
    }

    public function validator($data)
    {
        return Validator::make($data, [
            'name' => ['required', 'exists:configs,name'],
            'key' => ['nullable']
        ], ['name.exists' => 'O nome da configuração informada é inválido.']);
    }
}
