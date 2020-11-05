<?php

namespace App\Models;

use App\Traits\FileManager;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory, FileManager, LogsActivity;

    protected $guarded = [];
    protected static $logName = 'clients';
    protected static $logUnguarded = true;
    protected static $logOnlyDirty = true;
    
    /**
     * Descrição que é cadastrada no log de atividades toda vez que um tipo
     * de evento ocorre no model
     * 
     * @param string $eventname
     * 
     * @return string
     */
    public function getDescriptionForEvent(string $eventName): string
    {
        if ($eventName == 'created') {
            return '
                <div data-event="created">
                    <strong>:causer.name</strong> 
                    cadastrou o cliente 
                    <strong>:subject.name</strong>
                </div>
            ';
        }

        if ($eventName == 'updated') {
            return '
                <div data-event="updated">
                    <strong>:causer.name</strong> 
                    alterou os dados do cliente 
                    <strong>:subject.name</strong>
                </div>
            ';
        }

        if ($eventName == 'deleted') {
            return '
                <div data-event="deleted">
                    <strong>:causer.name</strong> 
                    deletou o cliente 
                    <strong>:subject.name</strong>
                </div>
            ';
        }
    }

    /**
     * Método booted do model
     * 
     * @return void
     */
    public static function booted() 
    {
        static::deleting(function($client) {
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

    public function getNewOrderCode()
    {
        return substr($this->phone, -4);
    }
}
