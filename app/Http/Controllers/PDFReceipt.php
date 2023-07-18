<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use Carbon\Carbon;
use Illuminate\Support\Str;

class PDFReceipt extends PDFController
{
    public function __invoke(Receipt $receipt)
    {
        $date = Carbon::createFromFormat(
            'Y-m-d',
            $receipt->date
        )->format('d-m-Y');

        $clientName = Str::slug(explode(' ', $receipt->client)[0]);

        $filename = "recibo-$clientName-$date.pdf";

        return response()->file($receipt->filepath, [
            'Content-disposition' => 'inline; filename="' . $filename . '"'
        ]);
    }
}
