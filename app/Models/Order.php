<?php

namespace App\Models;

use Carbon\Carbon;
use App\Util\FileHelper;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\ClothingTypeResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory, LogsActivity;

    protected $guarded = [];
    protected static $logName = 'orders';
    protected static $logUnguarded = true;
    protected static $logOnlyDirty = true;
    protected static $logAttributes = ['client'];

    public function getDescriptionForEvent(string $eventName): string
    {
        if ($eventName == 'created') {
            return '
                <div data-event="created">
                    <strong>:causer.name</strong>
                    cadastrou o pedido
                    <strong>:subject.code</strong>
                    para o cliente
                    <strong>:properties.attributes.client.name</strong>
                </div>
            ';
        }

        if ($eventName == 'updated') {
            return '
                <div data-event="updated">
                    <strong>:causer.name</strong>
                    alterou os dados do pedido
                    <strong>:subject.code</strong>
                    do cliente
                    <strong>:properties.attributes.client.name</strong>
                </div>
            ';
        }

        if ($eventName == 'deleted') {
            return '
                <div data-event="deleted">
                    <strong>:causer.name</strong>
                    deletou o pedido
                    <strong>:subject.code</strong>
                    do cliente
                    <strong>:properties.attributes.client.name</strong>
                </div>
            ';
        }
    }

    public static function booted()
    {
        $FILE_FIELDS = [
            'art_paths',
            'size_paths',
            'payment_voucher_paths'
        ];

        static::creating(function (Order $order) {
            $order->status_id = Status::first()->id;
        });

        static::deleting(function (Order $order) use ($FILE_FIELDS) {
            foreach ($FILE_FIELDS as $field) {
                $files = json_decode($order->{$field}) ?? [];

                FileHelper::deleteFiles($files, $field);
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'code';
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
        $commissionClothingTypes = collect(
            json_decode($this->commission->seam_commission)
        )->map(function ($type) {
            return [
                'key' => $type->key,
                'quantity' => $type->quantity
            ];
        });

        $clothingTypes = $this->clothingTypes->map(function ($type) {
            return [
                'key' => $type->key,
                'quantity' => $type->pivot->quantity
            ];
        });

        $hasDifference = $clothingTypes->pluck('key')
            ->diff($commissionClothingTypes->pluck('key'));

        if (!$hasDifference->isEmpty()) {
            return true;
        }

        foreach ($clothingTypes as $type) {
            $commission = $commissionClothingTypes
                ->where('key', $type['key'])
                ->first();

            if ($type['quantity'] != $commission['quantity']) {
                return true;
            }
        }

        return false;
    }

    public function scopePreRegistered($query)
    {
        return $query->whereNull('quantity')->orWhereNull('client_id');
    }

    public function getCommissions()
    {
        return ClothingTypeResource::collection($this->clothingTypes);
    }

    public function getOriginalPrice()
    {
        return bcadd($this->price, $this->discount, 2);
    }

    public function clothingTypes()
    {
        return $this->belongsToMany(ClothingType::class)
            ->withPivot('quantity', 'value');
    }

    public function totalClothingsValue()
    {
        $total = 0;

        foreach ($this->clothingTypes as $type) {
            $total = bcadd($type->totalValue(), $total, 2);
        }

        return $total;
    }

    public function createDownPayment($value, $viaId)
    {
        return $this->payments()->create([
            'value' => $value,
            'date' => Carbon::now(),
            'payment_via_id' => $viaId,
            'note' => 'Pagamento de entrada'
        ]);
    }

    public function getTotalPaid()
    {
        return $this->payments()
            ->where('is_confirmed', true)
            ->sum('value');
    }

    public function getReminder()
    {
        return $this->notes()->whereNotNull('is_reminder')->first();
    }

    public function getTotalOwing()
    {
        return bcsub($this->price, $this->getTotalPaid(), 2);
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
        return $this->getTotalOwing() <= 0;
    }

    public function isClosed()
    {
        return $this->closed_at !== null;
    }

    public function isPreRegistered()
    {
        return $this->quantity === null || $this->client_id === null;
    }

    public function getStates()
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

    public function getPaths($field, $publicRelative = false)
    {
        $folderName = [
            'art_paths' => 'imagens_da_arte',
            'size_paths' => 'imagens_do_tamanho',
            'payment_voucher_paths' => 'comprovantes'
        ][$field];

        if (!$this->{$field}) {
            return [];
        }

        return array_map(function ($filename) use ($publicRelative, $folderName) {
            return $publicRelative
                ? "public/$folderName/$filename"
                : "/storage/$folderName/$filename";
        }, json_decode($this->{$field}));
    }
}
