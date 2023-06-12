<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class GarmentGarmentSize extends Pivot
{
    protected $fillable = [
        'quantity',
        'garment_size_id'
    ];

    public function size()
    {
        return $this->belongsTo(GarmentSize::class, 'garment_size_id');
    }
}
