<?php

namespace App\Http\Controllers;

use App\Filters\ClientFilter;
use App\Http\Resources\ClientResource;
use Illuminate\Http\Request;
use App\Models\Client;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'per-page' => ['numeric', 'min:1']
        ]);

        $query = Client::query();

        app(ClientFilter::class)->filter($query, $request->all());

        return ClientResource::collection(
            $query->paginate($request->input('per-page', 10))
        );
    }
}
