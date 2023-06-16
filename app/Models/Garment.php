<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Garment extends Model
{
    use HasFactory;

    protected $fillable = [
        'garment_match_id'
    ];

    protected $appends = [
        'value_per_unit',
        'quantity',
        'value',
        'sizes_value'
    ];


    public function getSizesValueAttribute()
    {
        $matchSizes = $this->match->sizes;

        return $this->sizes->reduce(function ($total, $size) use ($matchSizes) {
            $matched = $matchSizes->first(
                fn ($matchSize) => $size->id === $matchSize->id
            );

            $totalSize = bcmul(
                $matched->pivot->value,
                $size->pivot->quantity,
                2
            );

            return bcadd($total, $totalSize, 2);
        }, 0);
    }


    public function getValueAttribute()
    {
        return bcmul($this->value_per_unit, $this->quantity, 2);
    }

    public function getValuePerUnitAttribute()
    {
        $matchValues = $this->match->values;
        $quantity = $this->quantity;
        $value = $matchValues->first(
            fn ($value) => $value->start <= $quantity && $value->end >= $quantity
                || !$value->end
        );

        return $value->value;
    }

    public function getQuantityAttribute()
    {
        $INITIAL_VALUE = 0;

        return $this->sizes->reduce(
            fn ($total, $size) => bcadd($total, $size->pivot->quantity),
            $INITIAL_VALUE
        );
    }

    public function match()
    {
        return $this->belongsTo(GarmentMatch::class, 'garment_match_id')->withTrashed();
    }

    public function sizes()
    {
        return $this->belongsToMany(GarmentSize::class)
            ->withPivot(['id', 'quantity'])
            ->using(GarmentGarmentSize::class);
    }
}
