<?php

namespace App\Models;

use Carbon\Carbon;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory, LogsActivity;

    protected $guarded = [];
    protected static $logName = 'payments';
    protected static $logUnguarded = true;
    protected static $logOnlyDirty = true;
    protected static $logAttributes = ['order'];

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
                    registrou um pagamento de 
                    <span class="font-weight-bold" data-mask="money">:subject.value</span> 
                    para o pedido 
                    <strong>:properties.attributes.order.code</strong>
                </div>
            ';
        }

        if ($eventName == 'updated') {
            return '
                <div data-event="updated">
                    <strong>:causer.name</strong> 
                    alterou os dados de pagamento do pedido 
                    <strong>:properties.attributes.order.code</strong>
                </div>
            ';
        }

        if ($eventName == 'deleted') {
            return '
                <div data-event="deleted">
                    <strong>:causer.name</strong> 
                    deletou o pedido :subject.code do cliente 
                    <strong>:properties.attributes.client.name</strong>
                </div>
            ';
        }
    }

    public function via()
    {
        return $this->belongsTo(Via::class, 'payment_via_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function scopePendencies()
    {
        return $this->where(function ($query) {
            $query->whereNull('is_confirmed');
            $query->where('created_at', '<', Carbon::now()->toDateString());
        });
    }
}
