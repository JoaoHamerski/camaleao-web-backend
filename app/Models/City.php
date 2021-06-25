<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $appends = ['shipping_company'];
    protected $guarded = [];
    protected $with = ['state'];
    
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function getShippingCompanyAttribute()
    {
        return $this->branch->shippingCompany ?? null;
    }
}
