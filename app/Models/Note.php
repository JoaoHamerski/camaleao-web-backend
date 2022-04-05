<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Note extends Model
{
    use HasFactory, LogsActivity;
    protected static $logAlways = [
        'order.code'
    ];
    protected static $logFillable = true;
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    protected static $logName = 'notes';

    protected $fillable = [
        'text',
        'order_id',
        'is_reminder'
    ];

    public function getCreatedLog(): string
    {
        if ($this->is_reminder) {
            return $this->getDescriptionLog(
                static::$CREATE_TYPE,
                ':causer adicionou um lembrete ao pré-registrar um pedido: :subject',
                [':causer.name'],
                [':subject.text']
            );
        }

        return $this->getDescriptionLog(
            static::$CREATE_TYPE,
            ':causer adicionou uma anotação ao pedido :attribute: :subject',
            [':causer.name'],
            [':subject.text'],
            [':attributes.order.code']
        );
    }

    public function getUpdatedLog(): string
    {
        return $this->getDescriptionLog(
            static::$UPDATE_TYPE,
            ':causer alterou uma anotação do pedido :attribute',
            [':causer.name'],
            [],
            [':attributes.order.code']
        );
    }

    public function getDeletedLog(): string
    {
        return $this->getDescriptionLog(
            static::$DELETE_TYPE,
            ':causer deletou uma anotação do pedido :attribute: :subject',
            [':causer.name'],
            [':subject.text'],
            [':attributes.order.code']
        );
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
