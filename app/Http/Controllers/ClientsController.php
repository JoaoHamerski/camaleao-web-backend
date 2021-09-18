<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClientResource;
use App\Models\Client;
use App\Util\Sanitizer;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ClientsController extends Controller
{
    public function index(Request $request)
    {
        $clients = Client::query();

        if ($request->has('opcao') && !empty($request->opcao)) {
            if ($request->opcao === 'nome') {
                $clients->where('name', 'like', '%' . $request->busca . '%');
            }

            if ($request->opcao === 'telefone') {
                $phone = Sanitizer::removeNonDigits($request->busca);

                if (Str::containsAll($request->busca, ['(', ')'])) {
                    $clients->where('phone', 'like', $phone . '%');
                } else {
                    $clients->where('phone', 'like', '%' . $phone . '%');
                }
            }

            if ($request->opcao === 'cidade') {
                $clients->whereHas('city', function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->busca . '%');
                });
            }
        }

        return ClientResource::collection($clients->paginate(10));
    }

    public function list(Request $request)
    {
        $clients = Client::query();

        if ($request->filled('name')) {
            if (is_numeric($request->name)) {
                $clients = Client::where('phone', 'like', "%$request->name%");
            } else {
                $clients = Client::where('name', 'like', "%$request->name%");
            }
        }


        return response()->json([
            'clients' => $clients->limit(50)->get()
        ], 200);
    }

    public function show(Client $client, Request $request)
    {
        $orders = $client->orders();

        if ($request->has('codigo')) {
            $orders->where('code', 'like', '%' . $request->codigo . '%');
        }

        return view('clients.show', [
            'client' => $client,
            'orders' => $orders->latest()->paginate(10)->appends($request->query()),
            'cities' => Client::all()->pluck('city')->unique()->sort()
        ]);
    }

    public function client(Client $client)
    {
        $client = Client::with(['city', 'branch', 'shippingCompany'])
            ->find($client->id);

        return response()->json(['client' => $client], 200);
    }

    public function store(Request $request)
    {
        $validator = $this->validator(
            $data = $this->getFormattedData($request->all())
        );

        if ($validator->fails()) {
            return response()->json([
                'message' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        Client::create($data);

        return response()->json([
            'message' => 'success',
            'redirect' => route('clients.index')
        ], 200);
    }

    public function update(Request $request, Client $client)
    {
        $validator = $this->validator(
            $data = $this->getFormattedData($request->all())
        );

        if ($validator->fails()) {
            return response()->json([
                'message' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $client->update($data);

        return response()->json([
            'message' => 'success',
            'redirect' => $client->path()
        ], 200);
    }

    public function destroy(Request $request, Client $client)
    {
        if (!Hash::check($request->password, $request->user()->password)) {
            return response()->json([
                'errors' => [
                    'password' => ['A senha informada não confere.']
                ]
            ], 422);
        }

        $client->delete();

        return response()->json([
            'message' => 'success',
            'redirect' => route('clients.index')
        ], 200);
    }

    private function validator($data)
    {
        return Validator::make($data, [
            'name' => ['required', 'max:255'],
            'phone' => ['nullable', 'min:8', 'max:11'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'shipping_company_id' => ['nullable', 'exists:shipping_companies,id']
        ]);
    }

    private function getFormattedData(array $data)
    {
        $keys = ['branch_id', 'city_id', 'shipping_company_id'];

        foreach ($keys as $key) {
            if (isset($data[$key])) {
                $data[$key] = $data[$key]['id'];
            }
        }

        foreach ($data as $key => $field) {
            if (Str::contains($key, ['name']) && !empty($field)) {
                $data[$key] = Sanitizer::name($field);
            }

            if (Str::contains($key, ['phone']) && !empty($field)) {
                $data[$key] = Sanitizer::removeNonDigits($field);
            }
        }



        return $data;
    }
}
