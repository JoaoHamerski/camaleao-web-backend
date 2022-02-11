<?php

namespace App\GraphQL\Traits;

use App\Util\Helper;
use App\Models\Expense;
use App\Util\Formatter;
use App\Util\FileHelper;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\UploadedFile;

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
        $data = (new Formatter($data))
            ->date('date')
            ->base64ToUploadedFile('receipt_path')
            ->currencyBRL('value')
            ->get();

        return $data;
    }


    public function getReceiptPathRules($data, $maxFileSize)
    {
        $file = $data['receipt_path'] ?? null;

        return $file instanceof UploadedFile
            ? ['nullable', 'file', "max:$maxFileSize", 'mimes:jpg,jpeg,png,pdf']
            : ['nullable'];
    }

    public function getRequiredRule($expense)
    {
        return $expense
            ? 'nullable'
            : 'required';
    }

    public function validator(array $data, Expense $expense = null)
    {
        $MAX_RECEIPT_SIZE = 1024;

        return Validator::make($data, [
            'id' => ['sometimes', 'exists:expenses,id'],
            'description'  => [$this->getRequiredRule($expense)],
            'value' => [$this->getRequiredRule($expense)],
            'expense_type_id' => [$this->getRequiredRule($expense), 'exists:expense_types,id'],
            'expense_via_id' => [$this->getRequiredRule($expense), 'exists:vias,id'],
            'receipt_path' => $this->getReceiptPathRules($data, $MAX_RECEIPT_SIZE),
            'date' => [$this->getRequiredRule($expense), 'date_format:Y-m-d'],
        ], $this->errorMessages($MAX_RECEIPT_SIZE));
    }

    public function errorMessages($MAX_RECEIPT_SIZE)
    {
        return [
            'expense_type_id.required' => __('general.validation.type_required'),
            'expense_via_id.required' => __('general.validation.via_required'),
            'description.required' => __('general.validation.expenses.description'),
            'receipt_path.max' => __('general.validation.expenses.receipt_path_file_max', [
                'size' => Helper::formatKBytes($MAX_RECEIPT_SIZE)
            ])
        ];
    }
}