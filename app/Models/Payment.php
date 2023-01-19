<?php

namespace App\Models;

use Carbon\Carbon;
use App\Util\Helper;
use App\Models\Entry;
use Illuminate\Support\Arr;
use App\Traits\EntriesTrait;
use App\Traits\LogsActivity;
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
        'is_shipping',
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
            static::onCreated($payment);
        });

        static::updated(function ($payment) {
            static::onUpdated($payment);
        });

        static::deleted(function ($payment) {
            static::onDeleted($payment);
        });
    }

    public static function onCreated(Payment $payment)
    {
        if (!empty($payment->bank_uid)) {
            Entry::where('bank_uid', $payment->bank_uid)->delete();
        }
    }

    public static function onDeleted(Payment $payment)
    {
        if ($payment->bank_uid) {
            static::restoreEntryFromPayment($payment);
        }

        if ($payment->is_shipping) {
            static::reduceValueFromOrder($payment);
        }
    }

    public static function onUpdated(Payment $payment)
    {
        if (!empty($payment->bank_uid)) {
            Entry::where('bank_uid', $payment->bank_uid)->delete();
        }

        if ($payment->clientBalances->count()) {
            $payment->clientBalances()->update([
                'is_confirmed' => $payment->is_confirmed
            ]);
        }
    }

    public static function restoreEntryFromPayment(Payment $payment)
    {
        $data = $payment->only([
            'bank_uid',
            'value',
        ]);

        $data['description'] = $payment->note ?? 'N/A';
        $data['via_id'] = $payment->payment_via_id;
        $data['date'] = Carbon::createFromFormat('Y-m-d', $payment->date)->format('d/m/Y');

        Entry::create($data);
    }

    public static function reduceValueFromOrder($payment)
    {
        $order = $payment->order;

        $shippingValue = bcsub($order->shipping_value, $payment->value, 2);
        $price = bcsub($order->price, $payment->value, 2);

        $payment->order->update([
            'shipping_value' => $shippingValue,
            'price' => $price
        ]);
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

    public function fillConfirmation(bool $confirmation = true)
    {
        $this->fill([
            'confirmed_at' => Carbon::now(),
            'is_confirmed' => $confirmation
        ]);

        if ($this->is_shipping && $confirmation) {
            $this->sumPaymentToOrderPrice();
        }
    }

    public function sumPaymentToOrderPrice()
    {
        $shippingValue = bcadd($this->value, $this->order->shipping_value, 2);
        $price = bcadd($this->order->price, $this->value, 2);

        $this->order->update([
            'shipping_value' => $shippingValue,
            'price' => $price
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
