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

    protected $appends = ['is_available'];

    /**
     * Indica qual ID da tabela é um status "disponível para retirada"
     */
    protected $AVAILABLE_ID = 8;

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
        return $this->id === $this->AVAILABLE_ID;
    }
}
