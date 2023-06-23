<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClothingType extends Model
{
    use HasFactory, LogsActivity;

    protected static $logFillable = true;
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    protected static $logName = 'clothing_types';

    protected $fillable = [
        'key',
        'name',
        'is_hidden',
        'order',
    ];

    protected $appends = [
        'quantity',
        'value',
        'total_value'
    ];

    public function getCreatedLog(): string
    {
        return $this->getDescriptionLog(
            static::$CREATE_TYPE,
            ':causer cadastrou um tipo de camisa: :subject',
            [':causer.name'],
            [':subject.name'],
        );
    }

    public function getUpdatedLog(): string
    {
        return $this->getDescriptionLog(
            static::$UPDATE_TYPE,
            ':causer alterou os dados do tipo de camisa :subject',
            [':causer.name'],
            [':subject.name']
        );
    }

    public function getDeletedLog(): string
    {
        return $this->getDescriptionLog(
            static::$DELETE_TYPE,
            ':causer deletou o tipo de camisa :subject',
            [':causer.name'],
            [':subject.name']
        );
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class);
    }

    public function getQuantityAttribute()
    {
        if ($this->pivot) {
            return $this->pivot->quantity;
        }

        return null;
    }

    public function getValueAttribute()
    {
        if ($this->pivot) {
            return $this->pivot->value;
        }

        return null;
    }

    public function getTotalValueAttribute()
    {
        if ($this->pivot) {
            return bcmul(
                $this->pivot->quantity,
                $this->pivot->value,
                2
            );
        }

        return null;
    }
}
