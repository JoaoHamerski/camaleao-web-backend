<?php

namespace App\Models;

use App\Traits\EntriesTrait;
use Carbon\Carbon;
use App\Traits\LogsActivity;
use App\Util\Helper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use EntriesTrait, HasFactory, LogsActivity;

    protected static $logAlways = [
        'via.name',
        'order.code'
    ];
    protected static $logFillable = true;
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    protected static $logName = 'payments';

    protected $fillable = [
        'bank_uid',
        'note',
        'date',
        'payment_via_id',
        'value',
        'is_confirmed',
        'confirmed_at',
        'created_at',
        'sponsorship_client_id'
    ];

    protected $appends = [
        'is_sponsor'
    ];

    protected static function booted()
    {
        static::created(function ($payment) {
            if (!empty($payment->bank_uid)) {
                Entry::where('bank_uid', $payment->bank_uid)->delete();
            }
        });

        static::updated(function ($payment) {
            if ($payment->clientBalances->count()) {
                $payment->clientBalances()->update([
                    'is_confirmed' => $payment->is_confirmed
                ]);
            }
        });
    }

    public function getCreatedLog(): string
    {
        return $this->getDescriptionLog(
            static::$CREATE_TYPE,
            ':causer registrou o pagamento de :subject para o pedido :attribute',
            [':causer.name'],
            [':subject.value'],
            [':attributes.order.code']
        );
    }

    public function getUpdatedLog(): string
    {
        return $this->getDescriptionLog(
            static::$UPDATE_TYPE,
            ':causer alterou o pagamento de :subject no pedido :attribute',
            [':causer.name'],
            [':subject.value'],
            [':attributes.order.code']
        );
    }

    public function getDeletedLog(): string
    {
        return $this->getDescriptionLog(
            static::$DELETE_TYPE,
            ':causer deletou o pagamento de :subject no pedido :attribute',
            [':causer.name'],
            [':subject.value'],
            [':attributes.order.code']
        );
    }

    public function via()
    {
        return $this->belongsTo(Via::class, 'payment_via_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function makeConfirm()
    {
        $this->fill([
            'confirmed_at' => Carbon::now(),
            'is_confirmed' => true
        ]);
    }

    public function sponsorshipClient()
    {
        return $this->belongsTo(Client::class);
    }

    public function getIsSponsorAttribute()
    {
        return !!$this->sponsorship_client_id;
    }

    public function scopePendencies(Builder $builder = null, bool $pendencies = true)
    {
        $builder = $builder ?? $this;

        if (!$pendencies) {
            return $builder;
        }

        return $builder->where(function ($query) {
            $query->whereNull('is_confirmed')
                ->orWhereNull('confirmed_at');

            $query->whereDate('created_at', '<', Carbon::now()->toDateString());
        });
    }

    public function clientBalances()
    {
        return $this->hasMany(ClientBalance::class);
    }
}
