<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GarmentMatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'model_id',
        'material_id',
        'neck_type_id',
        'sleeve_type_id',
        'unique_value'
    ];

    public function model()
    {
        return $this->belongsTo(\App\Models\Model::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function neckType()
    {
        return $this->belongsTo(NeckType::class);
    }

    public function sleeveType()
    {
        return $this->belongsTo(SleeveType::class);
    }

    public function sizes()
    {
        return $this->belongsToMany(GarmentSize::class)
            ->orderBy('order')
            ->withPivot('id', 'value')
            ->using(GarmentMatchGarmentSize::class);
    }

    public function values()
    {
        return $this->belongsToMany(GarmentValue::class)
            ->orderBy('start');
    }
}
