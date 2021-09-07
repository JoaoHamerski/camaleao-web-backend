<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Secret;

class SecretsController extends Controller
{
    public function index()
    {
        return Secret::all();
    }
}
