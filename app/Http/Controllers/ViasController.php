<?php

namespace App\Http\Controllers;

use App\Models\Via;
use Illuminate\Http\Request;

class ViasController extends Controller
{
    public function list()
    {
        return response()->json([
            'vias' => Via::all()
        ], 200);
    }
}
