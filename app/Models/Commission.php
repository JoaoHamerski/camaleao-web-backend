<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Return the user total comission value.
     *
     * @param $user App\Models\User | int Instance of user model or user id
     *
     * return double
     */
    public function getUserCommission($user)
    {
        if (! $user instanceof User) {
            $user = User::find($user);
        }

        if ($user->hasRole('costura')) {
            return $this->getSeamTotalCommission();
        }

        return $this->getPrintTotalCommission();
    }

    public function getPrintTotalCommission()
    {
        return bcmul(
            $this->print_commission,
            $this->order->quantity,
            2
        );
    }

    public function getSeamTotalCommission()
    {
        $commissions = json_decode($this->seam_commission);
        $total = 0;

        foreach ($commissions as $commission) {
            $total = bcadd(
                $total,
                bcmul($commission->commission, $commission->quantity, 2),
                2
            );
        }

        return $total;
    }
}
