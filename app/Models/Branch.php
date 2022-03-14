<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_id',
        'shipping_company_id'
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function cities()
    {
        return $this->hasMany(City::class);
    }

    public function shippingCompany()
    {
        return $this->belongsTo(ShippingCompany::class);
    }
}
