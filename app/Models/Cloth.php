<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cloth extends Model
{
    use HasFactory;

    protected $fillable = [
        'quantity',
        'cloth_match_id'
    ];

    protected $table = 'clothes';


    public function clothMatch()
    {
        return $this->belongsTo(ClothMatch::class);
    }
}
