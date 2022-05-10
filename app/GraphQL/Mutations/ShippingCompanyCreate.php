<?php

namespace App\GraphQL\Mutations;

use App\Models\ShippingCompany;
use App\Util\Formatter;
use Illuminate\Support\Facades\Validator;

class ShippingCompanyCreate
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'name' => ['required', 'max:191']
        ], $this->errorMessages())->validate();

        return ShippingCompany::create($args);
    }

    public function errorMessages()
    {
        return [
            'name.required' => __('validation.rules.required')
        ];
    }
}
