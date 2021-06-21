<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $with = ['shippingCompany', 'cities', 'city'];

    public function shippingCompany()
    {
        return $this->belongsTo(ShippingCompany::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function cities()
    {
        return $this->hasMany(City::class);
    }
}
