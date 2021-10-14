<?php

namespace App\Http\Controllers;

use App\Http\Resources\BranchResource;
use App\Models\City;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BranchesController extends Controller
{
    public function index()
    {
        $branches = Branch::with([
            'city' => function ($query) {
                $query->orderBy('name');
            },
            'cities'
        ]);

        return BranchResource::collection($branches->get());
    }

    public function store(Request $request)
    {
        $this->validator(
            $data = $this->getFormattedData($request->all())
        )->validate();

        $branch = Branch::create([
            'city_id' => $data['branch_id'],
            'shipping_company_id' => $data['shipping_company_id']
        ]);

        City::whereIn('id', $data['cities_id'])->update([
            'branch_id' => $branch->id
        ]);

        return response()->json([], 201);
    }

    public function update(Request $request, Branch $branch)
    {
        $this->validator(
            $data = $this->getFormattedData($request->all())
        )->validate();

        $branch->update([
            'city_id' => $data['branch_id'],
            'shipping_company_id' => $data['shipping_company_id']
        ]);

        City::whereIn('id', $branch->cities->pluck('id'))
            ->update([
                'branch_id' => null
            ]);

        City::whereIn('id', $data['cities_id'])->update([
            'branch_id' => $branch->id
        ]);

        return response()->json([], 200);
    }

    public function destroy(Branch $branch)
    {
        $branch->delete();

        return response()->json([], 204);
    }

    public function list(Request $request)
    {
        $branches = Branch::with([
            'city' => function ($query) {
                $query->orderBy('name');
            },
            'cities'
        ]);

        if ($request->filled('no_paginate') && $request->no_paginate == true) {
            $branches = $branches->get();
        } else {
            $branches = $branches->paginate(10);
        }

        return response()->json(['branches' => $branches], 200);
    }

    public function getFormattedData(array $data)
    {
        $fields = ['branch_id', 'shipping_company_id'];

        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $data[$field] = $data[$field]['id'];
            }
        }

        if (isset($data['cities_id'])) {
            $data['cities_id'] = array_column($data['cities_id'], 'id');
        }

        return $data;
    }

    public function validator(array $data)
    {
        return Validator::make($data, [
            'branch_id' => ['required', 'exists:cities,id'],
            'shipping_company_id' => ['required', 'exists:shipping_companies,id'],
            'cities_id' => ['required', 'exists:cities,id']
        ], $this->errorMessages());
    }

    public function errorMessages()
    {
        return [
            'branch_id.required' => 'Por favor, selecione a filial',
            'shipping_company_id.required' => 'Por favor, selecione a transportadora',
            'cities_id.required' => 'Por favor, selecione as cidades'
        ];
    }
}
