<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entry extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_uid',
        'value',
        'date',
        'description',
        'via_id',
        'is_canceled'
    ];

    protected $casts = [
        'is_canceled' => 'boolean'
    ];
}
