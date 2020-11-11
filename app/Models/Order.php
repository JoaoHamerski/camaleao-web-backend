<?php

namespace App\Models;

use App\Traits\FileManager;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory, FileManager, LogsActivity;

    protected $guarded = [];
    protected static $logName = 'orders';
    protected static $logUnguarded = true;
    protected static $logOnlyDirty = true;
    protected static $logAttributes = ['client'];

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

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'code';
    }

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
     * @return array
     */
    public function getPaths($field, $publicRelative = false) 
    {
        $folderName = [
            'art_paths' => 'imagens_da_arte',
            'size_paths' => 'imagens_do_tamanho',
            'payment_voucher_paths' => 'comprovantes'
        ][$field];

        if (! $this->{$field})
            return [];

        return array_map(function($filename) use ($publicRelative, $folderName) {
            return $publicRelative
                ? "public/$folderName/$filename"
                : "/storage/$folderName/$filename"; 
        }, json_decode($this->{$field}));
    }

    public function isClosed()
    {
        return $this->closed_at != null;
    }
}
