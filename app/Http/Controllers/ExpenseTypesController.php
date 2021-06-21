<?php

namespace App\Http\Controllers;

use App\Models\ExpenseType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExpenseTypesController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'expense_type' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $expenseType = ExpenseType::create([
            'name' => $request->expense_type
        ]);

        return response()->json([
            'message' => 'success',
            'view' => view('expenses.partials.expense-type-item', [
                'expenseType' => $expenseType
            ])->render()
        ], 200);
    }

    public function patch(ExpenseType $expenseType, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'expense_type_updated' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $expenseType->name = $request->expense_type_updated;
        $expenseType->save();

        return response()->json([
            'message' => 'success',
            'view' => view('expenses.partials.expense-type-item', [
                'expenseType' => $expenseType
            ])->render()
        ], 200);
    }

    public function destroy(ExpenseType $expenseType)
    {
        $expenseType->delete();

        return response()->json([
            'message' => 'success'
        ], 200);
    }
}
