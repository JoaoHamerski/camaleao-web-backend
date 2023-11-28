<?php

namespace App\Models;

use App\Util\FileHelper;
use App\Traits\LogsActivity;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class Order extends Model
{
    use HasFactory, LogsActivity;

    protected static $logAlways = [
        'client.name'
    ];

    protected static $logAttributes = [
        'status.text'
    ];

    protected static $logFillable = true;
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    protected static $logName = 'orders';

    protected $fillable = [
        'name',
        'code',
        'client_id',
        'status_id',
        'quantity',
        'discount',
        'price',
        'delivery_date',
        'seam_date',
        'print_date',
        'art_paths',
        'size_paths',
        'payment_voucher_paths',
        'closed_at',
        'order',
        'final_status',
        'shipping_value',
        'recommendation_bonus_percent',
        // 'created_at'
    ];

    protected $appends = [
        'original_price',
        'reminder',
        'total_paid',
        'total_owing',
        'states',
        'art_paths',
        'size_paths',
        'payment_voucher_paths',
        'total_clothings_value',
        'total_garments_value',
        'has_individual_names'
    ];

    public function getCreatedLog(): string
    {
        if (!$this->code) {
            return $this->getDescriptionLog(
                static::$CREATE_TYPE,
                ':causer pré-cadastrou um pedido',
                [':causer.name']
            );
        }

        return $this->getDescriptionLog(
            static::$CREATE_TYPE,
            ':causer cadastrou o pedido :subject para o cliente :attribute',
            [':causer.name'],
            [':subject.code'],
            [':attributes.client.name']
        );
    }

    public function getUpdatedLog(): string
    {
        return $this->getDescriptionLog(
            static::$UPDATE_TYPE,
            ':causer alterou o pedido :subject do cliente :attribute',
            [':causer.name'],
            [':subject.code'],
            [':attributes.client.name']
        );
    }

    public function getDeletedLog(): string
    {
        return $this->getDescriptionLog(
            static::$DELETE_TYPE,
            ':causer deletou o pedido :subject do cliente :attribute',
            [':causer.name'],
            [':subject.code'],
            [':attributes.client.name']
        );
    }

    public static function booted()
    {
        $FILE_FIELDS = [
            'art_paths',
            'size_paths',
            'payment_voucher_paths'
        ];

        static::creating(function (Order $order) {
            if (!$order->status_id) {
                $defaultStatus = Status::find(22);

                $order->status_id = $defaultStatus
                    ? $defaultStatus->id
                    : Status::first()->id;
            }
        });

        static::created(function (Order $order) {
            $order->attachAllStatus($order);
            $order->confirmStatusOnCreated();
        });

        static::deleting(function (Order $order) use ($FILE_FIELDS) {

            foreach ($FILE_FIELDS as $field) {
                $files = FileHelper::getFilesFromField($order->{$field});

                FileHelper::deleteFiles($files, $field);
            }
        });

        static::deleted(function (Order $order) {
            static::removeClientBonus($order);
        });
    }

    public static function removeClientBonus($order)
    {
        if (!$order->client->clientRecommended) {
            return;
        }

        $recommendedClient = $order->client->clientRecommended;
        $bonus = $order->bonus->value;

        $bonusUpdated = bcsub($recommendedClient->bonus, $bonus, 2);

        $recommendedClient->update([
            'bonus' => $bonusUpdated < 0 ? 0 : $bonusUpdated
        ]);
    }

    public function confirmStatusOnCreated()
    {
        $ids = [];
        $status = $this->linkedStatus;
        $orderStatusIndex = $this->linkedStatus->search(
            fn ($status) => $status->id === $this->status->id
        );

        $status = $status->slice(0, $orderStatusIndex);

        array_push($ids, $this->status->id);
        array_push($ids, ...$status->pluck('id')->toArray());

        foreach ($ids as $id) {
            $this->confirmLinkedStatus($id);
        }
    }

    public function cancelLinkedStatus($status)
    {
        $this->linkedStatus()->updateExistingPivot(
            data_get($status, 'id', $status),
            [
                'is_confirmed' => false,
                'confirmed_at' => null,
                'user_id' => Auth::id()
            ]
        );
    }

    public function confirmLinkedStatus($status, $updateConfirmedAt = true)
    {
        $data = [
            'is_confirmed' => true,
            'user_id' => Auth::id()
        ];

        $data = $updateConfirmedAt
            ? array_merge($data, ['confirmed_at' => now()])
            : $data;

        $this->linkedStatus()->updateExistingPivot(
            data_get($status, 'id', $status),
            $data
        );
    }

    public function syncStatus()
    {
        if ($this->closed_at) {
            throw new Exception('Pedido fechado, não é possível sincronizar os status.');
            return;
        }

        $status = $this->linkedStatus;
        $orderStatus = $this->status;

        $status->each(function ($_status) use ($orderStatus) {
            $isConfirmed = $orderStatus->order >= $_status->order ? 1 : 0;

            $this->linkedStatus()->updateExistingPivot(
                $_status->id,
                ['is_confirmed' => $isConfirmed]
            );
        });
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    public function garments()
    {
        return $this->hasMany(Garment::class);
    }

    public function bonus()
    {
        return $this->hasOne(Bonus::class);
    }

    public function paidWithBonus()
    {
        return $this->payments()->where('is_bonus', true)->sum('value');
    }

    public function getTotalGarmentsValueAttribute()
    {
        $INITIAL_VALUE = 0;

        return $this->garments->reduce(function ($total, $garment) {
            $totalGarment = bcadd($garment->value, $garment->sizesValue, 2);
            return bcadd($totalGarment, $total, 2);
        }, $INITIAL_VALUE);
    }

    public function getHasOrderControlAttribute()
    {
        return !!$this->linkedStatus()->count();
    }

    public function attachAllStatus()
    {
        $status = Status::all();

        $status->each(function ($_status) {
            $this->linkedStatus()->attach($_status->id);
        });
    }

    public function linkedStatus()
    {
        return $this->belongsToMany(Status::class)
            ->using(OrderStatus::class)
            ->withPivot(['id', 'is_confirmed', 'confirmed_at', 'user_id'])
            ->orderBy('order', 'asc');
    }

    public static function getBySector($sector): Builder
    {
        if (!($sector instanceof Sector)) {
            $sector = Sector::find($sector);
        }

        return static::whereIn(
            'status_id',
            $sector->status->pluck('id')
        )->whereNull('closed_at');
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function getSectorWithRematchedStatus()
    {
        return $this->status->getSectorWithRematchedStatus();
    }

    public function sector()
    {
        return $this->status->sector();
    }

    public function scopePreRegistered(Builder $query)
    {
        return $query->whereNull('quantity')
            ->orWhereNull('price')
            ->orWhereNull('client_id');
    }

    public function scopeNotPreRegistered(Builder $query)
    {
        return $query->whereNotNull('quantity')
            ->whereNotNull('price')
            ->whereNotNull('client_id');
    }

    public function clothingTypes()
    {
        return $this->belongsToMany(ClothingType::class)
            ->withPivot('quantity', 'value');
    }

    public function getTotalClothingsValueAttribute()
    {
        $total = 0;

        foreach ($this->clothingTypes as $type) {
            $total = bcadd($type->total_value, $total, 2);
        }

        return $total;
    }

    public function getHasSponsorAttribute()
    {
        return $this->payments()
            ->where('is_confirmed', true)
            ->whereNotNull('sponsorship_client_id')
            ->exists();
    }

    public function getHasIndividualNamesAttribute()
    {
        return $this->garments->contains(fn ($garment) => $garment->individual_names);
    }

    public function getTotalPaidNonSponsorAttribute()
    {
        return bcsub($this->total_paid, $this->total_paid_sponsor, 2);
    }

    public function getTotalPaidSponsorAttribute()
    {
        return $this->payments()
            ->where('is_confirmed', true)
            ->whereNotNull('sponsorship_client_id')
            ->sum('value');
    }

    public function getTotalPaidAttribute()
    {
        return $this->payments()
            ->where('is_confirmed', true)
            ->sum('value');
    }

    public function getReminderAttribute()
    {
        $reminder = $this
            ->notes()
            ->whereNotNull('is_reminder')
            ->first();

        return $reminder ? $reminder->text : null;
    }

    public function getOriginalPriceAttribute()
    {
        return bcadd($this->price, $this->discount, 2);
    }

    public function getTotalOwingAttribute()
    {
        return bcsub($this->price, $this->total_paid, 2);
    }

    public function getTotalPossibleOwing()
    {
        $totalPayments = $this->payments()
            ->where(function ($query) {
                $query->where('is_confirmed', null)
                    ->orWhere('is_confirmed', true);
            })->sum('value');

        return bcsub($this->price, $totalPayments, 2);
    }

    public function isPaid()
    {
        if ($this->price === null) {
            return false;
        }

        return $this->total_owing <= 0;
    }

    public function isClosed()
    {
        return $this->closed_at !== null;
    }

    public function isPreRegistered()
    {
        return $this->quantity === null || $this->client_id === null;
    }

    public function isConcluded($statusConcluded, $field = null)
    {
        if ($field) {
            $collection = collect(AppConfig::get('status', 'conclude_status_map'));
            $statusConcluded = $collection->firstWhere('field', '==', $field)['status'];
        }

        $this->is_concluded = in_array($this->status->id, $statusConcluded);

        return $this;
    }

    public function canBeConcluded($statusCanBeConcluded, $field = null)
    {
        if ($field) {
            $collection = collect(AppConfig::get('status', 'update_status_map'));
            $statusCanBeConcluded = $collection->firstWhere('field', '==', $field)['status_is'];
        }

        $this->can_be_concluded = in_array($this->status->id, $statusCanBeConcluded);

        return $this;
    }

    public function getStatesAttribute()
    {
        $states = [];

        if ($this->isPaid()) {
            $states[] = 'PAID';
        }

        if ($this->isClosed()) {
            $states[] = 'CLOSED';
        }

        if ($this->isPreRegistered()) {
            $states[] = 'PRE-REGISTERED';
        }

        return $states;
    }

    public function getArtPathsAttribute($value)
    {
        return FileHelper::getFilesURL($value, 'art_paths');
    }

    public function getSizePathsAttribute($value)
    {
        return FileHelper::getFilesURL($value, 'size_paths');
    }

    public function getPaymentVoucherPathsAttribute($value)
    {
        return FileHelper::getFilesURL($value, 'payment_voucher_paths');
    }
}
