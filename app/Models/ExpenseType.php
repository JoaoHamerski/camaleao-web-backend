<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseType extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Um tipo de despesa tem varias despesas
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function expenses()
    {
    	return $this->hasMany(Expense::class);
    }
}
