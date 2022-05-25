<?php

namespace App\GraphQL\Mutations;

use App\Models\ProductType;
use App\GraphQL\Traits\ProductTypeTrait;

class ProductTypeCreate
{
    use ProductTypeTrait;

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $this->validator($args)->validate();

        $productType = ProductType::create($args);

        return $productType;
    }
}
