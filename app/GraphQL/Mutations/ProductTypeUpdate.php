<?php

namespace App\GraphQL\Mutations;

use App\Models\ProductType;
use App\GraphQL\Traits\ProductTypeTrait;

class ProductTypeUpdate
{
    use ProductTypeTrait;

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $this->validator($args)->validate();

        $productType = ProductType::find($args['id']);
        $productType->update(['name' => $args['name']]);

        return $productType;
    }
}
