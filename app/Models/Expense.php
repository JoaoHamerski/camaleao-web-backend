<?php

namespace App\Models;

use App\Util\FileHelper;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expense extends Model
{
    use HasFactory, LogsActivity;

    protected $guarded = [];
    protected static $logName = 'expenses';
    protected static $logUnguarded = true;
    protected static $logOnlyDirty = true;

    /**
     * Descrição que é cadastrada no log de atividades toda vez que um tipo
     * de evento ocorre no model
     *
     * @param string $eventname
     *
     * @return string
     */
    public function getDescriptionForEvent(string $eventName): string
    {
        if ($eventName == 'created') {
            return '
                <div data-event="created">
                    <strong>:causer.name</strong>
                    cadastrou uma despesa
                    <strong>":subject.description"</strong>
                    com data de
                    <strong data-mask="date">:subject.date</strong>
                    no valor de
                    <strong data-mask="money">:subject.value</strong>
                </div>
            ';
        }

        if ($eventName == 'updated') {
            return '
                <div data-event="updated">
                    <strong>:causer.name</strong>
                    alterou os dados da despesa
                    <strong>":subject.description"</strong>
                </div>
            ';
        }

        if ($eventName == 'deleted') {
            return '
                <div data-event="deleted">
                    <strong>:causer.name</strong> deletou a despesa
                    <strong>":subject.description"</strong>
                </div>
            ';
        }
    }

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
}
