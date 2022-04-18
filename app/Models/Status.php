<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    /**
     * Tabela para ser usada no model
     */
    protected $table = 'status';

    /**
     * Fillable properties
     */
    protected $fillable = ['text'];

    /**
     * Um status tem vários pedidos
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function getIsAvailableAttribute()
    {
        $availableIds = AppConfig::get('app', 'status_available');

        return in_array($this->id, $availableIds);
    }
}
