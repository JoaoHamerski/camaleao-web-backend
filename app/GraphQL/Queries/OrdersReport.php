<?php

namespace App\GraphQL\Queries;

use App\Util\Helper;
use App\Util\Formatter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;

class OrdersReport
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $data = $this->getFormattedData($args);
        $this->validator($data)->validate();

        return URL::temporarySignedRoute(
            'pdf.orders-report',
            now()->addMinutes(10),
            array_filter($data, fn ($item) => $item !== '')
        );
    }

    public function getFormattedData($data)
    {
        return (new Formatter($data))
            ->date(['closed_at', 'delivery_date'])
            ->get();
    }

    public function validator($data)
    {
        return Validator::make($data, [
            'city_id' => ['nullable', 'exists:cities,id'],
            'status_id' => ['nullable', 'exists:status,id'],
            'closed_at' => ['nullable', 'date'],
            'delivery_date' => ['nullable', 'date']
        ]);
    }
}
