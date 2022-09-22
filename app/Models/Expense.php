<?php

namespace App\Models;

use App\Traits\EntriesTrait;
use App\Util\FileHelper;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expense extends Model
{
    use EntriesTrait, HasFactory, LogsActivity;

    protected static $logAlways = [
        'type.name',
        'via.name',
        'product_type.name',
        'employee.name'
    ];
    protected static $logFillable = true;
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    protected static $logName = 'expenses';
    protected $fillable = [
        'bank_uid',
        'description',
        'date',
        'expense_type_id',
        'expense_via_id',
        'employee_id',
        'product_type_id',
        'receipt_path',
        'value',
        'is_confirmed',
        'confirmed_at'
    ];

    protected $appends = ['receipt_path'];

    public function getCreatedLog(): string
    {
        return $this->getDescriptionLog(
            static::$CREATE_TYPE,
            ':causer cadastrou uma despesa de :subject (:attribute) via :attribute: :subject',
            [':causer.name'],
            [':subject.value', ':subject.description'],
            [':attributes.type.name', ':attributes.via.name']
        );
    }

    public function getUpdatedLog(): string
    {
        return $this->getDescriptionLog(
            static::$UPDATE_TYPE,
            ':causer alterou a despesa de :subject',
            [':causer.name'],
            [':subject.value']
        );
    }

    public function getDeletedLog(): string
    {
        return $this->getDescriptionLog(
            static::$DELETE_TYPE,
            ':causer deletou a despesa de :subject (:attribute)',
            [':causer.name'],
            [':subject.value'],
            [':attributes.type.name']
        );
    }

    public static function booted()
    {
        $FILE_FIELD = 'receipt_path';

        static::created(function ($expense) {
            if (!empty($expense->bank_uid)) {
                Entry::where('bank_uid', $expense->bank_uid)->delete();
            }
        });

        static::deleting(function ($expense) use ($FILE_FIELD) {
            if (!empty($expense->{$FILE_FIELD})) {
                FileHelper::deleteFile(
                    $expense->{$FILE_FIELD},
                    $FILE_FIELD
                );
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function type()
    {
        return $this->belongsTo(ExpenseType::class, 'expense_type_id');
    }

    public function via()
    {
        return $this->belongsTo(Via::class, 'expense_via_id');
    }

    public function productType()
    {
        return $this->belongsTo(ProductType::class);
    }

    public function employee()
    {
        return $this->belongsTo(User::class);
    }

    public function getReceiptPathAttribute($value)
    {
        $path = FileHelper::getFilesURL($value, 'receipt_path');

        if (is_array($path)) {
            return null;
        }

        return $path;
    }

    public function confirm()
    {
        activity()->withoutLogs(function () {
            $this->update([
                'is_confirmed' => true,
                'confirmed_at' => now()
            ]);
        });
    }
}
