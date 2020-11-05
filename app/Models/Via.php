<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Via extends Model
{
    use HasFactory;

    /**
     * Uma via tem vários pagamentos
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments()
    {
    	return $this->hasMany(Payment::class);
    }

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
