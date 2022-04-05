<?php

namespace App\GraphQL\Mutations;

use App\Models\ClothingType;
use App\GraphQL\Traits\ClothingTypeTrait;

class ClothingTypeUpdate
{
    use ClothingTypeTrait;

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $data = $this->getFormattedData($args);


        $this->validator($data, true)->validate();
        $clothingType = ClothingType::find($data['id']);

        $clothingType->update($data);

        return $clothingType;
    }
}
