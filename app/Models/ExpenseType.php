<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class ExpenseType extends Model
{
    use HasFactory, LogsActivity;

    protected $guarded = [];
    protected static $logName = 'expenses_type';
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
	                criou um novo tipo de despesa
	                <strong>":subject.name"</strong>
	            </div>
	        ';
    	}

    	if ($eventName == 'updated') {
    		return '
    			<div data-event="updated">
    				<strong>:causer.name</strong>
    				alterou o tipo de despesa
    				<strong>":subject.name"</strong>
    			</div>
    		';
    	}

    	if ($eventName == 'deleted') {
    		return '
    			<div data-event="deleted">
    				<strong>:causer.name</strong>
    				deletou o tipo de despesa
    				<strong>"subject.name"</strong>
    			</div>
    		';
    	}
    }

    /**
     * Um tipo de despesa tem varias despesas
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function expenses()
    {
    	return $this->hasMany(Expense::class);
    }
}
