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

    protected $fillable = [
        'name',
        'code',
        'client_id',
        'status_id',
        'quantity',
        'discount',
        'price',
        'delivery_date',
        'production_date',
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
                $files = FileHelper::getFilesFromField($order->{$field});

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

    public function scopePreRegistered($query)
    {
        return $query->whereNull('quantity')->orWhereNull('client_id');
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

    /**
     * Cria uma pagamento de entrada.
     *
     * @param string|float $value Valor do pagamento
     * @param string|int $viaId Via do pagamento
     * @return \App\Models\Payment
     */
    public function createDownPayment($value, $viaId): Payment
    {
        return $this->payments()->create([
            'value' => $value,
            'date' => Carbon::now(),
            'payment_via_id' => $viaId,
            'note' => 'Pagamento de entrada'
        ]);
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
        return $this->getTotalOwingAttribute() <= 0;
    }

    public function isClosed()
    {
        return $this->closed_at !== null;
    }

    public function isPreRegistered()
    {
        return $this->quantity === null || $this->client_id === null;
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
