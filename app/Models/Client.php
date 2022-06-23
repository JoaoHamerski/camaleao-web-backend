<?php

namespace App\Models;

use App\Util\FileHelper;
use App\Traits\LogsActivity;
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
        'shipping_company_id'
    ];

    protected $cascadeDeletes = ['orders', 'payments'];

    protected $appends = ['total_owing'];

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

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function payments()
    {
        return $this->hasManyThrough(Payment::class, Order::class);
    }

    public function sponsorPayments()
    {
        return $this->hasMany(Payment::class, 'sponsorship_client_id', 'id');
    }

    public function getTotalOwingAttribute()
    {
        return bcsub($this->getTotalBought(), $this->getTotalPaid(), 2);
    }

    public function getTotalPaid()
    {
        return $this->payments()
            ->where('is_confirmed', true)
            ->whereNull('sponsorship_client_id')
            ->sum('value');
    }

    public function getTotalBought()
    {
        return $this->orders()->sum('price');
    }

    public function getNewOrderCode()
    {
        return substr($this->phone, -4);
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
