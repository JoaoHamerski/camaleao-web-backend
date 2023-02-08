<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderStatus extends Pivot
{
    use HasFactory;

    protected $table = 'order_status';
    protected $fillable = ['user_id', 'status_id', 'created_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }
}
