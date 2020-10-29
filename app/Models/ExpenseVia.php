<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseVia extends Model
{
    use HasFactory;

    /**
     * Uma via tem várias despesas
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function expenses()
    {
    	return $this->hasMany(Expense::class);
    }
}
