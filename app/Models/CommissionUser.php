<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CommissionUser extends Pivot
{
    use HasFactory;

    protected $fillable = [
        'commission_value',
        'confirmed_at',
        'role_id',
        'user_id',
        'was_quantity_changed'
    ];

    public function commission()
    {
        return $this->belongsTo(Commission::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function isConfirmed()
    {
        return !!$this->confirmed_at;
    }
}
