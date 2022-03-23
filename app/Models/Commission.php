<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'print_commission',
        'seam_commission'
    ];

    protected $appends = [
        'seam_total_commission',
        'print_total_commission',
        'role_commission',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function getRoleCommissionAttribute()
    {
        if (!$this->pivot) {
            return null;
        }

        if ($this->pivot->role->name === 'Estampa') {
            return $this->print_total_commission;
        }

        if ($this->pivot->role->name === 'Costura') {
            return $this->seam_total_commission;
        }

        return null;
    }

    public function getUserCommission($user)
    {
        if (!$user instanceof User) {
            $user = User::find($user);
        }

        if (!$user) {
            return null;
        }

        if ($user->hasRole('costura')) {
            return $this->seam_total_commission;
        }

        if ($user->hasRole('estampa')) {
            return $this->print_total_commission;
        }
    }

    public function getPrintTotalCommissionAttribute()
    {
        return bcmul(
            $this->print_commission,
            $this->order->quantity,
            2
        );
    }

    public function getSeamTotalCommissionAttribute()
    {
        $INITIAL_VALUE = 0;
        $seamCommissions = json_decode($this->seam_commission);

        return array_reduce($seamCommissions, function ($total, $item) {
            $clothingCommission = bcmul($item->commission, $item->quantity, 2);

            return bcadd($total, $clothingCommission, 2);
        }, $INITIAL_VALUE);
    }
}
