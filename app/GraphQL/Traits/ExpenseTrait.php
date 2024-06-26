<?php

namespace App\GraphQL\Traits;

use App\Models\AppConfig;
use App\Util\Helper;
use App\Models\Expense;
use App\Util\Formatter;
use App\Util\FileHelper;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;

trait ExpenseTrait
{
    private function isFileDeleted($expense, $file, $field)
    {
        return $expense
            && !empty($expense->{$field})
            && $file === '';
    }

    private function isFileStored($file)
    {
        return $file instanceof UploadedFile;
    }

    public function handleFilesUpload($data, Expense $expense = null)
    {
        $FILE_FIELD = 'receipt_path';
        $file = $data[$FILE_FIELD] ?? null;

        if ($this->isFileDeleted($expense, $file, $FILE_FIELD)) {
            FileHelper::deleteFile(
                $expense->{$FILE_FIELD},
                $FILE_FIELD
            );
        }

        if ($this->isFileStored($file)) {
            $data[$FILE_FIELD] = FileHelper::uploadFileToField(
                $file,
                $FILE_FIELD
            );
        }

        return $data;
    }

    public function getFormattedData(array $data, Expense $expense = null)
    {
        $product_type_expense_id = AppConfig::get('app', 'product_types_expense');
        $employee_expense_id = AppConfig::get('app', 'employee_expense');

        $data = (new Formatter($data))
            ->date('date')
            ->base64ToUploadedFile('receipt_path')
            ->currencyBRL('value')
            ->get();

        if ($product_type_expense_id !== $data['expense_type_id']) {
            unset($data['product_type_id']);
        }

        if ($employee_expense_id !== $data['expense_type_id']) {
            unset($data['employee_id']);
        }

        return $data;
    }


    public function getReceiptPathRules($data, $maxFileSize)
    {
        $file = $data['receipt_path'] ?? null;

        return $file instanceof UploadedFile
            ? ['nullable', 'file', "max:$maxFileSize", 'mimes:jpg,jpeg,png,pdf']
            : ['nullable'];
    }

    public function validator(array $data, Expense $expense = null)
    {
        $MAX_RECEIPT_SIZE = 1024;
        $product_type_expense_id = AppConfig::get('app', 'product_types_expense');
        $employee_expense_id = AppConfig::get('app', 'employee_expense');

        return Validator::make($data, [
            'bank_uid' => [
                'nullable',
                Rule::unique('expenses')->ignore($expense->bank_uid ?? null, 'bank_uid'),
                'exists:entries,bank_uid'
            ],
            'id' => ['sometimes', 'exists:expenses,id'],
            'description'  => ['required'],
            'value' => ['required'],
            'product_type_id' => [
                'nullable',
                Rule::requiredIf(
                    $data['expense_type_id'] === $product_type_expense_id
                ),
                'exists:product_types,id'
            ],
            'employee_id' => [
                'nullable',
                Rule::requiredIf(
                    $data['expense_type_id'] === $employee_expense_id
                ),
                'exists:users,id'
            ],
            'expense_type_id' => ['required', 'exists:expense_types,id'],
            'expense_via_id' => ['required', 'exists:vias,id'],
            'receipt_path' => $this->getReceiptPathRules($data, $MAX_RECEIPT_SIZE),
            'date' => ['required', 'date_format:Y-m-d'],
        ], $this->errorMessages($MAX_RECEIPT_SIZE));
    }

    public function errorMessages($MAX_RECEIPT_SIZE)
    {
        return [
            'bank_uid.unique' => __('validation.custom.expenses.unique'),
            'expense_type_id.required' => __('validation.rules.required_list', ['pronoun' => 'um']),
            'expense_via_id.required' => __('validation.rules.required_list', ['pronoun' => 'uma']),
            'description.required' => __('validation.rules.required'),
            'product_type_id.required' => __('validation.rules.required_list', ['pronoun' => 'um']),
            'employee_id.required' => __('validation.rules.required_list', ['pronoun' => 'um']),
            'value.required' => __('validation.rules.required'),
            'date.required' => __('validation.rules.required'),
            'date.date_format' => __('validation.rules.date_format'),
            'receipt_path.max' => __('validation.rules.max_file', [
                'max' => Helper::formatKBytes($MAX_RECEIPT_SIZE)
            ])
        ];
    }
}
