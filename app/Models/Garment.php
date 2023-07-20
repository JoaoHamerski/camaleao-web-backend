<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Garment extends Model
{
    use HasFactory;

    protected $fillable = [
        'garment_match_id',
        'individual_names'
    ];

    protected $appends = [
        'value_per_unit',
        'quantity',
        'value',
        'sizes_value'
    ];

    public function getIndividualNamesAttribute($value)
    {
        if (!$value) {
            return null;
        }

        $names = collect(json_decode($value, true));

        return $names->map(function ($name, $key) {
            $size = GarmentSize::find($name['size_id']);

            return [
                'id' => $key,
                'name' => $name['name'],
                'number' => $name['number'],
                'size' => $size->name,
                'size_id' => $size->id
            ];
        })->toArray();
    }

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
        $quantity = $this->quantity;

        if ($this->match->unique_value) {
            return $this->match->unique_value;
        }

        return $this->match->values->first(
            fn ($value) => $value->start <= $quantity && $value->end >= $quantity
                || !$value->end
        )->value;
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
            ->orderBy('order', 'ASC')
            ->withPivot(['id', 'quantity'])
            ->using(GarmentGarmentSize::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
