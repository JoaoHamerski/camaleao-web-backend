<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    protected $table = 'status';
    protected $fillable = ['sector_id', 'text', 'order'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function users()
    {
        return $this->sector->users();
    }

    public function sector()
    {
        return $this->belongsTo(Sector::class);
    }

    public function getSectorWithRematchedStatus()
    {
        $sectors = Sector::ordered();

        $index = $sectors->search(function ($sector) use ($sectors) {
            $ids = static::getStatusIdsToMatch($sector, $sectors);

            if (in_array($this->id, $ids)) {
                return true;
            }
        });

        return $sectors[$index];
    }

    public static function getNextStatus($status)
    {
        $nextStatusOrder = $status->order + 1;

        return Status::where('order', $nextStatusOrder)->first();
    }

    public static function getStatusIdsToMatch($sector, $sectors = null): array
    {
        $sectors = $sectors ? $sectors : Sector::ordered();
        $ids = collect();
        $status = static::getAllStatusExceptLast($sector);
        $index = $sectors->search(
            fn ($_sector) => $_sector->id === $sector->id
        );

        $ids->push($status);

        if (!static::isFirstSector($sector, $sectors)) {
            $previousSector = static::getPreviousSector($sectors, $index);
            $lastStatus = $previousSector->status->last();

            $ids->push($lastStatus->id);
        }

        if (static::isLastSector($sector, $sectors)) {
            $ids->push($sector->status->last()->id);
        }

        return $ids->flatten()->toArray();
    }

    private static function getAllStatusExceptLast($sector)
    {
        $status = clone $sector->status;
        $status->pop();

        return $status->pluck('id')->toArray();
    }

    private static function getPreviousSector($sectors, $index)
    {
        return $sectors[$index - 1];
    }

    private static function isLastSector($sector, $sectors)
    {
        return $sector->id === $sectors->last()->id;
    }

    private static function isFirstSector($sector, $sectors)
    {
        return $sector->id === $sectors->first()->id;
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    public function getIsAvailableAttribute()
    {
        $availableIds = AppConfig::get('app', 'status_available');

        return in_array($this->id, $availableIds);
    }
}
