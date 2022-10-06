<?php

namespace App\GraphQL\Queries;

use App\Models\Receipt;
use Illuminate\Support\Facades\Validator;

class ReceiptProductSuggestions
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'max' => ['nullable', 'numeric']
        ])->validate();

        $receipts = Receipt::take($args['max'] ?? 20)->get();
        return $receipts->pluck('product')->unique();
    }
}
