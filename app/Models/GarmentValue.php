<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GarmentValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'value',
        'start',
        'end'
    ];
}
