<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClothingType extends Model
{
    use HasFactory;

    protected $guarded = [];

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

    public function totalValue()
    {
        return bcmul(
            $this->pivot->quantity,
            $this->pivot->value,
            2
        );
    }
}
