<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StorageController extends Controller
{
    public function get(Request $request, $filepath)
    {
        return response()->file(
            storage_path('app/public/' . $filepath)
        );
    }
}
