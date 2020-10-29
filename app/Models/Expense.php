<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;
    
    protected $guarded = [];

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
     * Uma despesa pertence a um tipo de despesa
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function expenseType()
    {
    	return $this->belongsTo(ExpenseType::class);
    }

    /**
     * Uma despesa pertence a uma via 
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function expenseVia()
    {
    	return $this->belongsTo(ExpenseVia::class);
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
