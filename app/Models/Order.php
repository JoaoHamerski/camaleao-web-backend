<?php

namespace App\Models;

use App\Traits\FileManager;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory, FileManager;

    protected $guarded = [];

    /**
     * Método "booted" do model
     * 
     * @return void
    **/
    public static function booted() 
    {
        static::creating(function(Order $order) {
            $order->status_id = Status::first()->id;
        });

        static::deleting(function(Order $order) {
            static::deleteFiles($order, [
                'art_paths', 'size_paths', 'payment_voucher_paths'
            ]);
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

    public function path() 
    {
        return route('orders.show', [$this->client, $this]);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function createDownPayment($value)
    {
        return $this->payments()->create([
            'value' => $value,
            'date' => \Carbon\Carbon::now(),
            'note' => 'Pagamento de entrada'
        ]);
    }

    public function getTotalPayments()
    {
        return $this->payments()->sum('value');
    }

    public function getUnityValue()
    {
        return bcdiv($this->price, $this->quantity, 2);
    }

    public function getTotalOwing()
    {
        return bcsub($this->price, $this->getTotalPayments(), 2);
    }

    public function getPaths($field, $publicRelative = false) 
    {
    	$paths = [];

    	if ($field == 'art_paths' && ! empty($this->{$field})) {
	    	foreach (json_decode($this->art_paths) as $path) {
                $paths[] = $publicRelative 
                    ? 'public/imagens_da_arte/' . $path
                    : '/storage/imagens_da_arte/' . $path;
	    	} 
    	}

    	if ($field == 'size_paths' && ! empty($this->{$field})) {
    		foreach (json_decode($this->size_paths) as $path) {
                $paths[] = $publicRelative
                    ? 'public/imagens_do_tamanho/' . $path
                    : '/storage/imagens_do_tamanho/' . $path;
    		}
    	}

    	if ($field == 'payment_voucher_paths' && ! empty($this->{$field})) {
    		foreach(json_decode($this->payment_voucher_paths) as $path) {
                $paths[] = $publicRelative 
                    ? 'public/comprovantes/' . $path
                    : '/storage/comprovantes/' . $path;
    		}
    	}

    	return $paths;
    }
}
