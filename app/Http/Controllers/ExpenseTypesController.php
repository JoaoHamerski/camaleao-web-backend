<?php

namespace App\Http\Controllers;

use App\Http\Resources\ExpenseTypeResource;
use App\Models\ExpenseType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExpenseTypesController extends Controller
{
    public function index()
    {
        return ExpenseTypeResource::collection(
            ExpenseType::latest()->get()
        );
    }

    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:191']
        ])->validate();

        ExpenseType::create([
            'name' => $request->name
        ]);

        return response('', 201);
    }

    public function patch(ExpenseType $expenseType, Request $request)
    {
        Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:191']
        ])->validate();

        $expenseType->update([
            'name' => $request->name
        ]);

        return response('', 201);
    }

    public function destroy(ExpenseType $expenseType)
    {
        $expenseType->delete();

        return response()->json([
            'message' => 'success'
        ], 200);
    }
}
