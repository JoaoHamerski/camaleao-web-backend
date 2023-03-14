<?php

namespace App\GraphQL\Queries;

use App\Models\Receipt;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class ReceiptUrl
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'id' => ['nullable', 'exists:receipts'],
            'preview' => ['nullable', 'boolean']
        ])->validate();

        if (isset($args['preview']) && $args['preview']) {
            return URL::temporarySignedRoute(
                'pdf.receipt-preview',
                now()->addMinute(1)
            );
        }

        $receipt = Receipt::find($args['id']);

        return URL::temporarySignedRoute(
            'pdf.receipt',
            now()->addMinutes(1),
            compact('receipt')
        );
    }
}
