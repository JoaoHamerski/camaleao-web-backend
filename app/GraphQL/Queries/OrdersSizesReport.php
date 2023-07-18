<?php

namespace App\GraphQL\Queries;

use App\Models\GarmentMatch;
use App\Models\Model;
use App\Models\Order;
use App\Util\Formatter;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

final class OrdersSizesReport
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        $input = $this->getFormattedData($args);

        Validator::make($input, [
            'initial_date' => ['required', 'date'],
            'final_date' => ['nullable', 'date']
        ])->validate();

        return URL::temporarySignedRoute(
            'pdf.orders-sizes',
            now()->addMinutes(100),
            $input
        );
    }



    public function getFormattedData(array $data)
    {
        return (new Formatter($data))
            ->date(['initial_date', 'final_date'])
            ->get();
    }
}
