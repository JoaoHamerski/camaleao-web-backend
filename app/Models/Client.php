<?php

namespace App\Models;

use App\Traits\FileManager;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory, FileManager;

    protected $guarded = [];
    
    /**
     * Método booted do model
     * 
     * @return void
     */
    public static function booted() 
    {
        static::deleting(function(Client $client) {
            static::deleteFiles($client->orders, [
                'art_paths', 'size_paths', 'payment_voucher_paths'
            ]);
        });
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
     * Retorna a URL para a página do cliente
     * 
     * @return string
     */
    public function path() {
        return route('clients.show', $this);
    }

    /**
     * Retorna o total que o cliente está devendo
     * 
     * @return double
     */
    public function getTotalOwing()
    {
    	return bcsub($this->getTotalBuyied(), $this->getTotalPaid(), 2);
    }

    /**
     * Retorna o total pago pelo cliente
     * 
     * @return double
     */
    public function getTotalPaid()
    {
    	return $this->payments()->sum('value');
    }

    /**
     * Retorna o total comprado pelo cliente
     * 
     * @return double
     */
    public function getTotalBuyied()
    {
    	return $this->orders()->sum('price');
    }
}
