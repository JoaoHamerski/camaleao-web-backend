<?php

namespace App\Models;

use App\Traits\FileManager;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory, FileManager;

    protected $guarded = [];
    
    public static function booted() 
    {
        static::deleting(function(Client $client) {
            static::deleteFiles($client->orders, [
                'art_paths', 'size_paths', 'payment_voucher_paths'
            ]);
        });
    }

    public function path() {
    	return route('clients.show', $this);
    }

    public function orders() 
    {
    	return $this->hasMany(Order::class);
    }

    public function payments()
    {
    	return $this->hasManyThrough(Payment::class, Order::class);
    }

    public function getTotalOwing()
    {
    	return bcsub($this->getTotalBuyied(), $this->getTotalPaid(), 2);
    }

    public function getTotalPaid()
    {
    	return $this->payments()->sum('value');
    }

    public function getTotalBuyied()
    {
    	return $this->orders()->sum('price');
    }
}
