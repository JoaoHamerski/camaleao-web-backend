<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClothMatch extends Model
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
        return $this->belongsToMany(ClothSize::class)
            ->orderBy('order')
            ->withPivot('id', 'value');
    }

    public function values()
    {
        return $this->belongsToMany(ClothValue::class)
            ->orderBy('start');
    }
}
