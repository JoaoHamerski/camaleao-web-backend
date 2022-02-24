<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'state_id',
        'branch_id'
    ];

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

    // public function shippingCompany()
    // {
    //     $branch =  Branch::find($this->branch_id) ?? null;

    //     if ($branch === null) {
    //         return null;
    //     }

    //     return ShippingCompany::find($branch->shipping_company_id);
    // }
}
