<?php

namespace App\Models;

use Carbon\Carbon;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory, LogsActivity;

    protected $guarded = [];

    public function via()
    {
        return $this->belongsTo(Via::class, 'payment_via_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function confirm()
    {
        $this->update([
            'confirmed_at' => Carbon::now(),
            'is_confirmed' => true
        ]);
    }

    public function scopePendencies(Builder $builder = null, bool $pendencies = true)
    {
        $builder = $builder ?? $this;

        if (!$pendencies) {
            return $builder;
        }

        return $builder->where(function ($query) {
            $query->whereNull('is_confirmed');
            $query->whereDate('created_at', '<', Carbon::now()->toDateString());
        });
    }
}
