<?php

namespace App\Models;

use Spatie\Activitylog\Traits\CausesActivity;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, CausesActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'remember'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Um usuário pertence a uma regra
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Verifica se o usuário possui determinada regra
     *
     * @param int | array $role
     * @return boolean
     */
    public function hasRole($role)
    {
        if (is_array($role)) {
            foreach ($role as $r) {
                if ($this->role()->where('name', $r)->exists()) {
                    return true;
                }
            }
        }

        return $this->role()->where('name', $role)->exists();
    }

    public function isAdmin()
    {
        return $this->hasRole('Gerencia');
    }

    /**
     * Um usuário possui muitas despesas
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function commissions()
    {
        return $this->belongsToMany(Commission::class)
            ->withPivot([
                'id',
                'commission_value',
                'confirmed_at',
                'was_quantity_changed',
                'role_id'
            ]);
    }

    public function secrets()
    {
        return $this->hasMany(Secret::class);
    }

    public function scopeProduction()
    {
        return $this->whereHas('role', function ($query) {
            $query->where('name', 'estampa');
            $query->orWhere('name', 'costura');
        });
    }

    public function isProduction()
    {
        return $this->hasRole('costura') || $this->hasRole('estampa');
    }
}
