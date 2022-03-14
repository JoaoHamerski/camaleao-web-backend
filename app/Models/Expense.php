<?php

namespace App\Models;

use App\Util\FileHelper;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'date',
        'expense_type_id',
        'expense_via_id',
        'receipt_path',
        'value'
    ];

    protected $appends = ['receipt_path'];

    /**
     * Método booted do model
     *
     * @return void
     */
    public static function booted()
    {
        $FILE_FIELD = 'receipt_path';

        static::deleting(function ($expense) use ($FILE_FIELD) {
            if (!empty($expense->{$FILE_FIELD})) {
                FileHelper::deleteFile(
                    $expense->{$FILE_FIELD},
                    $FILE_FIELD
                );
            }
        });
    }

    /**
     * Uma despesa pertence a um usuário
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Uma despesa pertence a um tipo de despesa
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(ExpenseType::class, 'expense_type_id');
    }

    /**
     * Uma despesa pertence a uma via
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function via()
    {
        return $this->belongsTo(Via::class, 'expense_via_id');
    }

    public function getReceiptPathAttribute($value)
    {
        return FileHelper::getFilesURL($value, 'receipt_path');
    }
}
