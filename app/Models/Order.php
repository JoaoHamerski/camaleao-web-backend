<?php

namespace App\Models;

use App\Util\FileHelper;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        'closed_at'
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
        'total_clothings_value'
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
                $order->status_id = Status::first()->id;
            }
        });

        static::deleting(function (Order $order) use ($FILE_FIELDS) {
            foreach ($FILE_FIELDS as $field) {
                $files = FileHelper::getFilesFromField($order->{$field});

                FileHelper::deleteFiles($files, $field);
            }
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

    public function commissions()
    {
        return $this->hasMany(Commission::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function commission()
    {
        return $this->hasOne(Commission::class);
    }

    public function isQuantityChanged()
    {
        $oldClothingTypes = collect(json_decode($this->commission->seam_commission));
        $newClothingTypes = $this->clothingTypes;

        $pluckKeyAndQuantity = fn ($clothingType) => [
            'key' => $clothingType->key,
            'quantity' => $clothingType->quantity
        ];

        $oldClothingTypes->transform($pluckKeyAndQuantity);
        $newClothingTypes->transform($pluckKeyAndQuantity);

        return $newClothingTypes->some(function ($newClothingType) use ($oldClothingTypes) {
            $index = $oldClothingTypes->search(
                fn ($item) => $item['key'] === $newClothingType['key']
            );

            return $oldClothingTypes[$index]['quantity'] !== $newClothingType['quantity'];
        });
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

    public function getCommissions()
    {
        return $this->clothingTypes;
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
