<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Note extends Model
{
    use HasFactory, LogsActivity;

    protected $guarded = [];
    protected static $logName = 'notes';
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
	        		adicionou a anotação 
	        		<strong>":subject.text"</strong> 
	        		ao pedido 
	        		<strong>:properties.attributes.order.code</strong>
        		</div>
        	';
        }

        if ($eventName == 'updated') {
            return '
            	<div data-event="updated">
	            	<strong>:causer.name</strong> 
	            	alterou uma anotação do pedido 
	            	<strong>:properties.attributes.order.code</strong>
            	</div>
        	';
        }

        if ($eventName == 'deleted') {
            return '
            	<div data-event="deleted">
	        		<strong>:causer.name</strong> 
	        		deletou a anotação 
	        		<strong>":subject.text"</strong> 
	        		do pedido 
	        		<strong>:properties.attributes.order.code</strong>
        		</div>
    		';
        }
    }

    /**
     * Uma anotação pertence a um pedido
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
    	return $this->belongsTo(Order::class);
    }
}
