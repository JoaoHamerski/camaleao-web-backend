<?php

namespace App\GraphQL\Mutations;

use Illuminate\Support\Str;
use App\Models\ClothingType;
use App\GraphQL\Traits\ClothingTypeTrait;

class ClothingTypeCreate
{
    use ClothingTypeTrait;

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $data = $this->getFormattedData($args);

        $this->validator($data)->validate();

        return ClothingType::create($data);
    }
}
