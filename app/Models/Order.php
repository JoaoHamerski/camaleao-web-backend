<?php

namespace App\Models;

use App\Traits\FileManager;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory, FileManager, LogsActivity;

    protected $guarded = [];
    protected static $logName = 'orders';
    protected static $logUnguarded = true;
    protected static $logOnlyDirty = true;
    protected static $logAttributes = ['client'];
    protected $appends = ['total_owing'];

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
        static::creating(function (Order $order) {
            $order->status_id = Status::first()->id;
        });

        static::deleting(function (Order $order) {
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

    public function getOriginalPrice()
    {
        return bcadd($this->price, $this->discount, 2);
    }

    public function clothingTypes()
    {
        return $this->belongsToMany(ClothingType::class)
            ->withPivot('quantity', 'value');
    }

    public function totalClothingsValue()
    {
        $total = 0;

        foreach ($this->clothingTypes as $type) {
            $total = bcadd($type->totalValue(), $total, 2);
        }

        return $total;
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
    public function createDownPayment($value, $viaId)
    {
        return $this->payments()->create([
            'value' => $value,
            'date' => \Carbon\Carbon::now(),
            'payment_via_id' => $viaId,
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
        return $this->payments()
            ->where('is_confirmed', true)
            ->sum('value');
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

    public function getTotalOwingAttribute()
    {
        return $this->getTotalOwing();
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

    /**
     * Verifica se o pedido está pago
     *
     * @return bool
     */
    public function isPaid()
    {
        return $this->getTotalOwing() <= 0;
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

        if (! $this->{$field}) {
            return [];
        }

        return array_map(function ($filename) use ($publicRelative, $folderName) {
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
