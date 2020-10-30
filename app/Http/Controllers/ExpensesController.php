<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\ExpenseVia;
use App\Util\Validate;
use App\Util\Sanitizer;
use App\Models\Expense;
use App\Models\ExpenseType;
use Illuminate\Http\Request;
use App\Traits\FileManager;
use Illuminate\Support\Facades\Validator;

class ExpensesController extends Controller
{   
    use FileManager;

    public function index() 
    {
    	return view('expenses.index', [
            'expenses' => Expense::latest()->paginate(10),
            'expenseTypes' => ExpenseType::all(),
            'expenseVias' => ExpenseVia::all()
        ]);
    }

    public function create()
    {
    	return view('expenses.create', [
            'expenseTypes' => ExpenseType::all(),
            'expenseVias' => ExpenseVia::all()
        ]);
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


        if (is_array($data[array_key_first($data)])) {
            $expenses = collect($data);

            Expense::insert($expenses->transpose()->map(function($expense, $key) {
                if (isset($expense[5])) {
                    $filename = $this->uploadFile(
                        $expense[5],
                        $this->getFilepath('receipt_path'),
                        $key
                    );
                }

                return [
                    'description' => $expense[0],
                    'value' => $expense[1],
                    'date' => $expense[2],
                    'expense_type_id' => $expense[3],
                    'expense_via_id' => $expense[4],
                    'receipt_path' => $filename ?? null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
            })->toArray());

        } else {
            if ($request->hasFile('receipt_path')) {
                $filename = $this->uploadFile(
                    $request->receipt_path, 
                    $this->getFilepath('receipt_path')
                );

                $data = array_replace($data, ['recept_path' => $filename]);
            }

            Expense::create($data);
        }

        return response()->json([
            'message' => 'success',
            'redirect' => route('expenses.index')
        ], 200);
    }

    public function patch(Expense $expense, Request $request) {
        $validator = $this->validator(
            $data = $this->getFormattedData($request->all())
        );

        if ($validator->fails()) {
            return response()->json([
                'message' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->hasFile('receipt_path')) {
            $filename = $this->uploadFile(
                $request->receipt_path,
                $this->getFilepath('receipt_path')
            );

            $data = array_replace($data, ['receipt_path' => $filename]);
        }

        $expense->update($data);

        return response()->json([
            'messsage' => 'success',
            'redirect' => route('expenses.index')
        ], 200);
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();

        return response()->json([
            'message' => 'success',
            'redirect' => route('expenses.index')
        ], 200);
    }

    public function destroyReceipt(Expense $expense)
    {
        $expense->destroyReceipt();

        return response()->json([
            'message' => 'success'
        ], 200);
    }

    public function report(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dia_inicial' => 'required|date_format:d/m/Y',
            'dia_final' => 'nullable|date_format:d/m/Y|after:dia_inicial'
        ]);

        if (! $request->wantsJson()) {
            if ($validator->fails()) 
                return abort(422);
        } else {
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'error',
                    'errors' => $validator->errors()
                ], 422);
            }

            return response()->json([
                'message' => 'success'
            ], 200);;
        }

        $expenses = Expense::query();

        $start_date = Carbon::createFromFormat('d/m/Y', $request->dia_inicial)->subDays(1);

        $end_date = ! empty($request->dia_final) 
            ? Carbon::createFromFormat('d/m/Y', $request->dia_final)
            : (new Carbon($start_date))->addDays(1);

        $expenses->whereBetween('date', [$start_date, $end_date]);

        $expensesByType = collect();

        foreach (ExpenseType::all() as $expenseType) {
            if ($expenseType->expenses()
                    ->whereBetween('date', [$start_date, $end_date])->exists()) {
                $expensesByType->put(
                    $expenseType->name, 
                    $expenseType
                        ->expenses()
                        ->whereBetween('date', [$start_date, $end_date])
                        ->sum('value')
                );
            }
        }

        $pdf = \PDF::loadView('expenses.pdf.report', [
            'expenses' => $expenses->orderBy('date', 'desc')->get(),
            'start_date' => $start_date->addDays(1),
            'end_date' => ! empty($request->dia_final) ? $end_date : '',
            'expensesByType' => $expensesByType->sortBy(function($value, $key) {
                return $value;
            })->reverse()
        ]);

        $filename = 'despesas';
        $filename .= \Helper::date($start_date, ' %d-%m-%Y');
        $filename .= ! empty($request->dia_final) ? \Helper::date($end_date, '_%d-%m-%Y') : '';

        return $pdf->stream($filename . '.pdf');
    }

    public function getViewReceipt(Expense $expense)
    {
        return response()->json([
            'message' => 'success',
            'view' => view('expenses._view-receipt', [
                'expense' => $expense
            ])->render()
        ], 200);
    }

    public function getFormattedData(array $data) 
    {
        if (is_array($data['value'])) {
            foreach($data['value'] as $key =>  $value) {
                $data['value'][$key] = Sanitizer::money($value);
            }
        } else {
            $data['value'] = Sanitizer::money($data['value']);
        }

        if (is_array($data['date'])) {
            foreach($data['date'] as $key => $value) {
                if (Validate::isDate($value)) {
                    $data['date'][$key] = Carbon::createFromFormat('d/m/Y', $data['date'][$key])->toDateString();
                }
            }
        } else {
            if (Validate::isDate($data['date'])) {
                $data['date'] = Carbon::createFromFormat('d/m/Y', $data['date'])->toDateString();
            }
        }

        return $data;
    }

    public function validator(array $data) 
    {
        $isArray = is_array($data[array_key_first($data)]);

        return Validator::make($data, [
            $isArray ? 'description.*' : 'description'  => 'required',
            $isArray ? 'expense_type_id.*' : 'expense_type_id' => 'required|exists:expense_types,id',
            $isArray ? 'expense_via_id.*' : 'expense_via_id' => 'required|exists:vias,id',
            $isArray ? 'value.*' : 'value' => 'required',
            $isArray ? 'date.*' : 'date' => 'required|date',
            $isArray ? 'receipt_path.*' : 'receipt_path' => 'nullable|file|mimes:jpg,jpeg,bmp,png,gif,svg,pdf'
        ]);
    }

    public function getEditForm(Expense $expense) 
    {
        return response()->json([
            'message' => 'success',
            'view' => view('expenses._form-modal', [
                'expense' => $expense,
                'expenseTypes' => ExpenseType::all(),
                'expenseVias' => ExpenseVia::all(),
                'method' => 'PATCH'
            ])->render()
        ], 200);
    }
    
    public function getInlineForm()
    {
    	return response()->json([
    		'message' => 'success',
    		'view' => view('expenses._inline-form', [
                'expenseTypes' => ExpenseType::all(),
                'expenseVias' => ExpenseVia::all()
            ])->render()
    	], 200);
    }
}
