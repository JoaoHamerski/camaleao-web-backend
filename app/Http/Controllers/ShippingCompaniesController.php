<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShippingCompany;
use Illuminate\Support\Facades\Validator;

class ShippingCompaniesController extends Controller
{
    public function store(Request $request)
    {
        $this->validator(
            $request->all()
        )->validate();

        ShippingCompany::create([
            'name' => $request->name
        ]);

        return response()->json([], 201);
    }

    public function update(Request $request, ShippingCompany $shippingCompany)
    {
        $this->validator(
            $request->all()
        )->validate();

        $shippingCompany->update([
            'name' => $request->name
        ]);

        return response()->json([], 200);
    }

    public function destroy(ShippingCompany $shippingCompany)
    {
        $shippingCompany->delete();

        return response()->json([], 204);
    }

    public function companies()
    {
        $companies = ShippingCompany::orderBy('name')->get();

        return response()->json(
            ['companies' => $companies],
            200
        );
    }

    public function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'max:191']
        ]);
    }
}
