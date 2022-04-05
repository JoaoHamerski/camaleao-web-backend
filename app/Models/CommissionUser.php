<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CommissionUser extends Pivot
{
    use HasFactory, LogsActivity;

    protected static $recordEvents = ['updated'];

    protected static $logAlways = [
        'order.code'
    ];
    protected static $logAttributes = [
        'confirmed_at',
        'was_quantity_changed'
    ];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    protected static $logName = 'commission_users';
    protected $fillable = [
        'role_id',
        'user_id',
        'confirmed_at',
        'commission_value',
        'was_quantity_changed'
    ];

    public function getUpdatedLog(): string
    {
        if ($this->confirmed_at) {
            return $this->getDescriptionLog(
                static::$UPDATE_TYPE,
                ':causer confirmou a produção do pedido :attribute',
                [':causer.name'],
                [],
                [':attributes.order.code']
            );
        }

        return $this->getDescriptionLog(
            static::$UPDATE_TYPE,
            ':causer alterou as quantidades do pedido :attribute, a produção precisa ser reconfirmada',
            [':causer.name'],
            [],
            [':attributes.order.code']
        );
    }

    public function commission()
    {
        return $this->belongsTo(Commission::class);
    }

    public function order()
    {
        return $this->commission->order();
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
