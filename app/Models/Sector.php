<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sector extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    public function orders()
    {
        return $this->hasManyThrough(Order::class, Status::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->orderByPivot('users.name');
    }

    public function scopeOrdered(Builder $query)
    {

        return $query
            ->join('status', 'sectors.id', '=', 'status.sector_id')
            ->select(['sectors.*', 'status.order'])
            ->orderBy('status.order')
            ->get()
            ->unique('id')
            ->values();
    }

    public function isLastSector()
    {
        $sectors = $this->ordered();

        return $sectors->last()->id === $this->id;
    }

    public function getCanCloseSectorOrdersAttribute()
    {
        return $this->isLastSector();
    }

    public function status()
    {
        return $this->hasMany(Status::class)
            ->orderBy('order');
    }
}