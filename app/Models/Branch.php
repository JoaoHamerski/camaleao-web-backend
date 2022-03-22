<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Branch extends Model
{
    use HasFactory, LogsActivity;

    protected $recordEvents = ['created', 'deleted'];
    protected static $logAlways = [
        'city.name',
        'shippingCompany.name',
    ];
    protected static $logAttributes = [
        'cities'
    ];
    protected static $logFillable = true;
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    protected static $logName = 'branches';

    protected $fillable = [
        'city_id',
        'shipping_company_id'
    ];

    public function getCreatedLog(): string
    {
        return $this->getDescriptionLog(
            static::$CREATE_TYPE,
            ':causer cadastrou a filial :attribute',
            [':causer.name'],
            [],
            [':attributes.city.name']
        );
    }

    // Update log feito manualmente na BranchUpdate mutation
    // App\GraphQL\Mutations\BranchUpdate.php

    public function getDeletedLog(): string
    {
        return $this->getDescriptionLog(
            static::$DELETE_TYPE,
            ':causer deletou a filial :attribute',
            [':causer.name'],
            [],
            [':attributes.city.name']
        );
    }

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
