<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GarmentSize extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'order'
    ];
}
