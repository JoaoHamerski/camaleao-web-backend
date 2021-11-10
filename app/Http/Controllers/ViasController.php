<?php

namespace App\Http\Controllers;

use App\Http\Resources\ViaResource;
use App\Models\Via;
use Illuminate\Http\Request;

class ViasController extends Controller
{
    public function index()
    {
        return ViaResource::collection(Via::all());
    }
}
