<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClientBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'value',
        'client_id',
        'payment_id',
        'is_confirmed'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
