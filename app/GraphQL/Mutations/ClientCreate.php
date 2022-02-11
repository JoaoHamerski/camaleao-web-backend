<?php

namespace App\GraphQL\Mutations;

use App\Models\Client;
use App\Util\Formatter;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class ClientCreate
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $data = $this->getFormattedData($args);

        $this->validator($data)->validate();

        return Client::create($data);
    }

    private function getFormattedData(array $data)
    {
        return (new Formatter($data))
            ->name('name')
            ->stripNonDigits('phone')
            ->get();
    }

    private function validator($data)
    {
        return Validator::make($data, [
            'name' => ['required', 'max:191'],
            'phone' => ['nullable', 'min:8', 'max:11'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'shipping_company_id' => ['nullable', 'exists:shipping_companies,id']
        ]);
    }
}
