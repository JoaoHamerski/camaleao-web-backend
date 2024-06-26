<?php

namespace App\Models;

use App\Util\FileHelper;
use App\Traits\LogsActivity;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory, LogsActivity;

    protected static $logAttributes = [
        'branchCity.name',
        'city.name',
        'shippingCompany.name'
    ];
    protected static $logFillable = true;
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    protected static $logName = 'clients';

    protected $fillable = [
        'name',
        'phone',
        'branch_id',
        'city_id',
        'shipping_company_id',
        'balance',
        'client_recommended_id',
        'bonus'
    ];

    protected $cascadeDeletes = ['orders', 'payments'];

    public function getCreatedLog(): string
    {
        return $this->getDescriptionLog(
            static::$CREATE_TYPE,
            ':causer cadastrou o cliente :subject',
            [':causer.name'],
            [':subject.name']
        );
    }

    public function getUpdatedLog(): string
    {
        return $this->getDescriptionLog(
            static::$UPDATE_TYPE,
            ':causer atualizou os dados do cliente :subject',
            [':causer.name'],
            [':subject.name']
        );
    }

    public function getDeletedLog(): string
    {
        return $this->getDescriptionLog(
            static::$DELETE_TYPE,
            ':causer deletou o cliente :subject',
            [':causer.name'],
            [':subject.name']
        );
    }

    public static function booted()
    {
        static::deleting(function ($client) {
            self::deleteFiles($client->orders);
        });
    }

    public static function deleteFiles($orders)
    {
        $FILE_FIELDS = [
            'art_paths',
            'size_paths',
            'payment_voucher_paths'
        ];

        foreach ($orders as $order) {
            foreach ($FILE_FIELDS as $field) {
                $filesToDelete = FileHelper::getFilesFromField($order->{$field});
                FileHelper::deleteFiles($filesToDelete, $field);
            }
        }
    }

    public function increaseBonus($value)
    {
        $bonus = $this->bonus;

        $this->update([
            'bonus' => bcadd($bonus, $value, 2)
        ]);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function payments()
    {
        return $this->hasManyThrough(Payment::class, Order::class);
    }

    public function balances()
    {
        return $this->hasMany(ClientBalance::class);
    }

    public function bonuses()
    {
        return $this->hasMany(Bonus::class);
    }

    public function clientRecommendations()
    {
        return $this->hasMany(Client::class, 'client_recommended_id');
    }

    public function clientRecommended()
    {
        return $this->belongsTo(Client::class, 'client_recommended_id');
    }

    public function getIsSponsorAttribute()
    {
        return $this->sponsorPayments()->exists();
    }

    public function getHasBalanceAttribute()
    {
        return ClientBalance::where('client_id', $this->id)->exists();
    }

    public function getBalanceAttribute()
    {
        return ClientBalance::where('client_id', $this->id)
            ->where('is_confirmed', true)
            ->sum('value');
    }

    public function sponsorPayments()
    {
        return $this->hasMany(Payment::class, 'sponsorship_client_id', 'id');
    }

    public function getTotalOwingAttribute()
    {
        return bcsub($this->getTotalBought(), $this->getTotalPaid(), 2);
    }

    public function getTotalOwingAsSponsorshipAttribute()
    {
        return $this->sponsorPayments()
            ->whereNull('is_confirmed')
            ->sum(DB::raw('ROUND(value, 2)'));
    }

    public function getTotalPaid()
    {
        return $this->payments()
            ->where('is_confirmed', true)
            ->sum('value');
    }

    public function getTotalBought()
    {
        return $this->orders()->sum('price');
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function shippingCompany()
    {
        return $this->belongsTo(ShippingCompany::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function branchCity()
    {
        if (!$this->branch) {
            return $this->branch();
        }

        return $this->branch->city();
    }
}
