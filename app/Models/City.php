<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory, LogsActivity;

    protected $appends = ['shipping_company'];
    protected $guarded = [];
    protected $with = ['state'];

    protected static $logName = 'cities';
    protected static $logUnguarded = true;
    protected static $logOnlyDirty = true;
    protected static $logAttributes = ['state'];

    public function getDescriptionForEvent(string $eventName): string
    {
        if ($eventName === 'created') {
            return '
                <div data-event="created">
                    <strong>:causer.name</strong>
                    cadastrou a cidade
                    <strong>:subject.name</strong>
                </div>
            ';
        }

        if ($eventName === 'updated') {
            return '
                <div data-event="updated">
                    <strong>:causer.name</strong>
                    alterou os dados da cidade
                    <strong>:subject.name</strong>
                </div>
            ';
        }

        if ($eventName === 'deleted') {
            return '
                <div data-event="deleted">
                    <strong>:causer.name</strong>
                    deletou a cidade
                    <strong>:subject.name</strong>
                </div>  
            ';
        }
    }

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
