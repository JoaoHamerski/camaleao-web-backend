<?php

namespace App\Models;

use Error;
use App\Models\Sector;
use Illuminate\Support\Arr;
use App\Traits\LogsActivity;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Config;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Validator;
use Spatie\Activitylog\Traits\CausesActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, CausesActivity, LogsActivity;

    protected static $logAlways = [
        'name',
        'email',
        'role.name'
    ];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    protected static $logName = 'users';

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

    public function getCreatedLog(): string
    {
        return $this->getDescriptionLog(
            static::$CREATE_TYPE,
            ':causer adicionou o usuário :subject com privilégio de :attribute',
            [':causer.name'],
            [':subject.name'],
            [':attributes.role.name']
        );
    }

    public function getUpdatedLog(): string
    {
        return $this->getDescriptionLog(
            static::$UPDATE_TYPE,
            ':causer alterou os dados do usuário :subject',
            [':causer.name'],
            [':subject.name']
        );
    }

    public function getDeletedLog(): string
    {
        return $this->getDescriptionLog(
            static::$DELETE_TYPE,
            ':causer deletou o usuário :subject de privilégio :attribute',
            [':causer.name'],
            [':subject.name'],
            [':attributes.role.name']
        );
    }

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
     * @param string|array $role
     * @return boolean
     */
    public function hasRole($roles)
    {
        $validator = Validator::make(compact('roles'), [
            'roles.*' => ['string']
        ]);

        if ($validator->fails()) {
            throw new Error(
                "As regras passadas devem ser apenas strings, verifique as regras no arquivo de configurações app.php roles"
            );
        }

        return $this
            ->role()
            ->whereIn('name', Arr::flatten([$roles]))
            ->exists();
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
            ->using(CommissionUser::class)
            ->withPivot([
                'id',
                'commission_value',
                'confirmed_at',
                'was_quantity_changed',
                'role_id'
            ]);
    }

    public function dailyCashReminders()
    {
        return $this->hasMany(DailyCashReminder::class);
    }

    public function secrets()
    {
        return $this->hasMany(Secret::class);
    }

    public function sectors()
    {
        return $this->belongsToMany(Sector::class);
    }

    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }

    public function scopeProduction()
    {
        $roles = Config::get('app.roles');

        return $this->whereHas('role', function ($query) use ($roles) {
            $query->where('id', $roles['ESTAMPA']);
            $query->orWhere('id', $roles['COSTURA']);
        });
    }

    public function isProduction()
    {
        return $this->hasRole('costura') || $this->hasRole('estampa');
    }
}
