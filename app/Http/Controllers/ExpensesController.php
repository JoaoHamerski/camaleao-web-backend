<?php

namespace App\Http\Controllers;

use App\Console\Kernel;
use Carbon\Carbon;
use App\Models\Via;
use App\Util\Helper;
use App\Util\Validate;
use App\Models\Expense;
use App\Util\Formatter;
use App\Util\FileHelper;
use Barryvdh\DomPDF\Facade as PDF;
use App\Models\ExpenseType;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Queries\ExpensesRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ExpenseResource;
use Illuminate\Support\Facades\Validator;

class ExpensesController extends Controller
{
    public function index(Request $request)
    {
        $expenses = ExpensesRequest::query($request);

        return ExpenseResource::collection($expenses->paginate(10));
    }

    public function store(Request $request)
    {
        $data = $this->getFormattedData($request->all());
        $this->validator($data)->validate();

        $data = $this->handleReceiptUpload($data);

        Auth::user()->expenses()->create($data);

        return response('', 201);
    }

    public function handleReceiptUpload($data, Expense $expense = null)
    {
        $FILE_FIELD = 'receipt_path';

        if ($expense) {
            if (Str::contains($data[$FILE_FIELD], $expense->{$FILE_FIELD})) {
                return $data;
            }

            if (!empty($expense->{$FILE_FIELD})) {
                FileHelper::deleteFile(
                    $expense->{$FILE_FIELD},
                    $FILE_FIELD
                );
            }
        }

        if (!empty($data[$FILE_FIELD])) {
            $filename = FileHelper::uploadFileToField(
                $data[$FILE_FIELD],
                $FILE_FIELD
            );

            $data[$FILE_FIELD] = $filename;
        }

        return $data;
    }

    public function update(Expense $expense, Request $request)
    {
        $data = $this->getFormattedData($request->all());
        $this->validator($data, $expense)->validate();

        $data = $this->handleReceiptUpload($data, $expense);
        $expense->update($data);

        return response('', 200);
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();

        return response('', 200);
    }

    public function reportValidator(array $data)
    {
        return Validator::make($data, [
            'start_date' => ['required', 'date_format:Y-m-d'],
            'end_date' => ['nullable', 'date_format:Y-m-d', 'after:start_date']
        ], [
            'start_date.required' => __('general.validation.start_date_required'),
            'end_date.after' => __('general.validation.end_date_after')
        ]);
    }

    public function validateReport(Request $request)
    {
        $data = Formatter::parse($request->all(), [
            'parseDate' => ['start_date', 'end_date']
        ]);

        $this->reportValidator($data)->validate();
    }

    public function report(Request $request)
    {
        $data = Formatter::parse($request->all(), [
            'parseDate' => ['start_date', 'end_date']
        ]);

        $this->reportValidator($data)->validate();

        $data['start_date'] = Carbon::createFromFormat('Y-m-d', $data['start_date'])->subDays(1);

        $data['end_date'] = $request->filled('end_date')
            ? Carbon::createFromFormat('Y-m-d', $data['end_date'])
            : (new Carbon($data['start_date']))->addDays(1);

        $expenses = ExpensesRequest::query($request, null, $data);

        $expensesByType = collect();

        foreach (ExpenseType::all() as $expenseType) {
            if (
                $expenseType->expenses()->whereBetween(
                    'date',
                    [
                        $data['start_date'],
                        $data['end_date']
                    ]
                )->exists()
            ) {
                $expensesByType->put(
                    $expenseType->name,
                    $expenseType
                        ->expenses()
                        ->whereBetween('date', [$data['start_date'], $data['end_date']])
                        ->sum('value')
                );
            }
        }

        $pdf = PDF::loadView('pdf.expenses-report', [
            'expenses' => $expenses->get(),
            'start_date' => $data['start_date']->addDays(1),
            'end_date' => $request->filled('end_date') ? $data['end_date'] : '',
            'expensesByType' => $expensesByType->sortBy(fn ($value) => $value)->reverse()
        ]);

        $filename = 'despesas';
        $filename .= Helper::date($data['start_date'], ' %d-%m-%Y');
        $filename .= $request->filled('end_date') ? Helper::date($data['end_date'], '_%d-%m-%Y') : '';

        return $pdf->stream($filename . '.pdf');
    }

    public function getFormattedData(array $data)
    {
        return Formatter::parse($data, [
            'parseDate' => ['date'],
            'base64toUploadedFile' => ['receipt_path'],
            'parseCurrencyBRL' => ['value']
        ]);
    }

    public function validator(array $data, Expense $expense = null)
    {
        $MAX_RECEIPT_SIZE = 1024;
        $FILE_FIELD = 'receipt_path';

        if ($expense) {
            $data[$FILE_FIELD] = Str::contains($data[$FILE_FIELD], $expense->{$FILE_FIELD})
                ? ''
                : $data[$FILE_FIELD];
        }

        return Validator::make($data, [
            'description'  => ['required'],
            'value' => ['required'],
            'expense_type_id' => ['required', 'exists:expense_types,id'],
            'expense_via_id' => ['required', 'exists:vias,id'],
            'receipt_path' => ['nullable', 'file', "max:$MAX_RECEIPT_SIZE", 'mimes:jpg,jpeg,png,pdf'],
            'date' => ['required', 'date_format:Y-m-d'],
        ], [
            'expense_type_id.required' => __('general.validation.type_required'),
            'expense_via_id.required' => __('general.validation.via_required'),
            'description.required' => __('general.validation.expenses.description'),
            'receipt_path.max' => __('general.validation.expenses.receipt_path_file_max', [
                'size' => Helper::formatKBytes($MAX_RECEIPT_SIZE)
            ])
        ]);
    }
}
