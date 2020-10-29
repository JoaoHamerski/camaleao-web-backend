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

    /**
     * Um pedido pertence a um cliente
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function client()
    {
    	return $this->belongsTo(Client::class);
    }

    /**
     * Um pedido tem muitos pagamentos
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Um pedido tem muitas anotações
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    /**
     * Um pedido pertence a um status
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    /**
     * Retorna a URL para a página do pedido
     * 
     * @return string
     */
    public function path() 
    {
        return route('orders.show', [$this->client, $this]);
    }

    /**
     * Cria um pagamento de entrada
     * 
     * @param double $value
     * 
     * @return App\Models\Payment
     */
    public function createDownPayment($value)
    {
        return $this->payments()->create([
            'value' => $value,
            'date' => \Carbon\Carbon::now(),
            'note' => 'Pagamento de entrada'
        ]);
    }

    /**
     * Retorna a soma total dos pagamentos feitos para o pedido
     * 
     * @return double
     */
    public function getTotalPayments()
    {
        return $this->payments()->sum('value');
    }

    /**
     * Retorna o valor unitário do pedido
     * 
     * @return double
     */
    public function getUnityValue()
    {
        return bcdiv($this->price, $this->quantity, 2);
    }

    /**
     * Retorna o total que falta pagar no pedido
     * 
     * @return double
     */
    public function getTotalOwing()
    {
        return bcsub($this->price, $this->getTotalPayments(), 2);
    }

    /**
     * Retorna o caminho para os arquivos do campo especificado
     * 
     * @param $field
     * @param $publicRelative Determina se o caminho deve ser relativo a pasta public
     * 
     * @return array $paths
     */
    public function getPaths($field, $publicRelative = false) 
    {
    	$paths = [];
        $relatedPaths = [
            'art_paths' => 'imagens_da_arte',
            'size_paths' => 'imagens_do_tamanho',
            'payment_voucher_paths' => 'comprovantes'
        ];

        foreach($relatedPaths as $fieldPath => $folderName) {
            if ($field == $fieldPath && ! empty($this->{$field})) {
                foreach(json_decode($this->{$field}) as $path) {
                    $paths[] = $publicRelative
                        ? "public/$folderName/$path"
                        : "/storage/$folderName/$path";
                }
            }
        }

    	return $paths;
    }
}
