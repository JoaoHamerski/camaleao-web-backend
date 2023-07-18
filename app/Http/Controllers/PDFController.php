<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PDFController extends Controller
{
    protected $generatedAt = '';
    protected static $FIELD_TYPES = [
        'seam_date' => 'Costuras',
        'print_date' => 'Estampas',
        'delivery_date' => 'Entregas',
    ];

    public function __construct(Request $request)
    {
        if (!$request->hasValidSignature()) {
            abort(401);
        }
    }
}
