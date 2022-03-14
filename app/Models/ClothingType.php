<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClothingType extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'is_hidden',
        'order',
        'commission'
    ];

    protected $appends = [
        'quantity',
        'value',
        'total_value'
    ];

    public function orders()
    {
        return $this->belongsToMany(Order::class);
    }

    public function getQuantityAttribute()
    {
        if ($this->pivot) {
            return $this->pivot->quantity;
        }

        return null;
    }

    public function getValueAttribute()
    {
        if ($this->pivot) {
            return $this->pivot->value;
        }

        return null;
    }

    public function getTotalValueAttribute()
    {
        if ($this->pivot) {
            return bcmul(
                $this->pivot->quantity,
                $this->pivot->value,
                2
            );
        }

        return null;
    }
}
