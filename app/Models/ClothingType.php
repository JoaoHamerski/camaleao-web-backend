<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClothingType extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $appends = [
        'quantity',
        'value',
        'total_value'
    ];

    /*
    Ordenação dos tipos de roupas
    REVISAR ISSO
    protected static function booted()
    {
        static::creating(function ($clothingType) {
            $clothingType->order = ClothingType::count();
        });
    }
    */

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
