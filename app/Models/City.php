<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory, LogsActivity;

    protected static $logAlways = [
        'state.name',
        'state.abbreviation'
    ];
    protected static $logFillable = true;
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    protected static $logName = 'cities';

    protected $fillable = [
        'name',
        'state_id',
        'branch_id'
    ];

    public function getCreatedLog(): string
    {
        return $this->getDescriptionLog(
            static::$CREATE_TYPE,
            ':causer cadastrou a cidade :subject (:attribute)',
            [':causer.name'],
            [':subject.name'],
            [':attributes.state.abbreviation']
        );
    }

    public function getUpdatedLog(): string
    {
        if (!$this->state) {
            return $this->getDescriptionLog(
                static::$UPDATE_TYPE,
                ':causer alterou a cidade :subject',
                [':causer.name'],
                [':subject.name']
            );
        }

        return $this->getDescriptionLog(
            static::$UPDATE_TYPE,
            ':causer alterou a cidade :subject (:attribute)',
            [':causer.name'],
            [':subject.name'],
            [':attributes.state.abbreviation']
        );
    }

    public function getDeletedLog(): string
    {
        if (!$this->state) {
            return $this->getDescriptionLog(
                static::$DELETE_TYPE,
                ':causer deletou a cidade :subject',
                [':causer.name'],
                [':subject.name']
            );
        }

        return $this->getDescriptionLog(
            static::$DELETE_TYPE,
            ':causer deletou a cidade :subject (:attribute)',
            [':causer.name'],
            [':subject.name'],
            [':attributes.state.abbreviation']
        );
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
}
