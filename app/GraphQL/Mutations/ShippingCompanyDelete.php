<?php

namespace App\GraphQL\Mutations;

use App\Models\ShippingCompany;
use Illuminate\Support\Facades\Validator;

class ShippingCompanyDelete
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'id' => ['required', 'exists:shipping_companies,id']
        ])->validate();

        $shippingCompany = ShippingCompany::find($args['id']);
        $shippingCompany->delete();

        return $shippingCompany;
    }
}
