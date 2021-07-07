<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Branch extends Model
{
    use HasFactory,LogsActivity;

    protected $guarded = [];
    protected $with = ['city'];

    protected static $logName = 'branches';
    protected static $logUnguarded = true;
    protected static $logOnlyDirty = true;
    protected static $logAttributes = ['city'];

    public function getDescriptionForEvent(string $eventName): string
    {
        if ($eventName === 'created') {
            return '
                <div data-event="created">
                    <strong>:causer.name</strong>
                    cadastrou a filial
                    <strong>:properties.attributes.city.name</strong>
                </div>
            ';
        }

        if ($eventName === 'updated') {
            return '
                <div data-event="updated">
                    <strong>:causer.name</strong>
                    alterou os dados da filial
                    <strong>:properties.attributes.city.name</strong>
                </div>
            ';
        }

        if ($eventName === 'deleted') {
            return '
                <div data-event="deleted">
                    <strong>:causer.name</strong>
                    deletou a filial
                    <strong>:properties.attributes.city.name</strong>
                </div>  
            ';
        }
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
