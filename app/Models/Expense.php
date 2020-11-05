<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        static::deleting(function($expense) {
            \Storage::delete($expense->receipt_path);
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

    /**
     * Retorna o caminho para o arquivo do comprovante
     * 
     * @return string
     */
    public function getReceiptPath() 
    {
        return $this->receipt_path 
            ? str_replace('public/', '/storage/', $this->receipt_path)
            : null;
    }

    /**
     * Delete o arquivo de comprovante
     * 
     * @return void
     */
    public function destroyReceipt()
    {
        \Storage::delete($this->receipt_path);
        
        $this->receipt_path = null;
        $this->save();
    }
}
