<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class GarmentGarmentSize extends Pivot
{
    protected $fillable = [
        'quantity',
        'garment_size_id'
    ];

    protected $appends = [
        'value'
    ];

    public function getValueAttribute()
    {
        if (!$this->pivotParent instanceof Garment) {
            return null;
        }

        $sizes = $this->pivotParent->match->sizes;
        $matchedSize = $sizes->first(fn ($size) => $size->id === $this->garment_size_id);

        return $matchedSize->pivot->value;
    }
}
