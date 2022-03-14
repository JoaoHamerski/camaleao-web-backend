<?php

namespace App\Models;

use App\Util\FileHelper;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'branch_id',
        'city_id',
        'shipping_company_id'
    ];

    protected $cascadeDeletes = ['orders', 'payments'];

    protected $appends = ['total_owing'];

    /**
     * Método booted do model
     *
     * @return void
     */
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

    /**
     * Um cliente tem muitos pedidos
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Um cliente tem muitos pagamentos de muitos pedidos
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function payments()
    {
        return $this->hasManyThrough(Payment::class, Order::class);
    }

    /**
     * Retorna o total que o cliente está devendo
     *
     * @return double
     */
    public function getTotalOwingAttribute()
    {
        return bcsub($this->getTotalBought(), $this->getTotalPaid(), 2);
    }

    /**
     * Retorna o total pago pelo cliente
     *
     * @return double
     */
    public function getTotalPaid()
    {
        return $this->payments()
            ->where('is_confirmed', true)
            ->sum('value');
    }

    /**
     * Retorna o total comprado pelo cliente
     *
     * @return double
     */
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
}
