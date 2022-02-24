<?php

namespace App\GraphQL\Mutations;

use App\Models\Config;
use App\GraphQL\Traits\ConfigTrait;
use App\Util\Helper;

class ConfigNew
{
    use ConfigTrait;

    const IS_NEW_ORDER = true;

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $data = $this->getFormattedData($args);

        $this->validator($data, self::IS_NEW_ORDER)->validate();

        if (Helper::filled($data, 'key')) {
            return Config::create([
                'name' => $data['name'],
                'json' => collect([$data['key'] => $data['value'] ?? ''])->toJson()
            ]);
        }

        return Config::create([
            'name' => $data['name']
        ]);
    }
}
