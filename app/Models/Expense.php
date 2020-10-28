<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    public static function booted()
    {
        static::deleting(function($expense) {
            \Storage::delete($expense->receipt_path);
        });
    }

    public function expenseType()
    {
    	return $this->belongsTo(ExpenseType::class);
    }

    public function expenseVia()
    {
    	return $this->belongsTo(ExpenseVia::class);
    }

    public function getReceiptPath() 
    {
        return $this->receipt_path 
            ? str_replace('public/', '/storage/', $this->receipt_path)
            : null;
    }

    public function destroyReceipt()
    {
        \Storage::delete($this->receipt_path);
        
        $this->receipt_path = null;
        $this->save();
    }
}
