<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entry extends Model
{
    use HasFactory;

    protected $appends = [
        'is_confirmed',
        'is_tied'
    ];

    protected $fillable = [
        'bank_uid',
        'value',
        'date',
        'description',
        'via_id',
        'is_canceled'
    ];

    protected $casts = [
        'is_canceled' => 'boolean'
    ];

    public function getIsPaymentConfirmed(): bool
    {
        $payment = Payment::where('bank_uid', $this->bank_uid)->first();

        if (!$payment) {
            return false;
        }

        return !!$payment->is_confirmed;
    }

    public function getIsExpenseConfirmed()
    {
        $expense = Expense::where('bank_uid', $this->bank_uid)->first();

        if (!$expense) {
            return false;
        }

        return $expense->is_confirmed;
    }

    public function getIsConfirmedAttribute()
    {
        if ($this->value > 0) {
            return $this->getIsPaymentConfirmed();
        }

        return $this->getIsExpenseConfirmed();
    }

    public function getIsPaymentTied()
    {
        $payment = Payment::where('bank_uid', $this->bank_uid)->first();

        if (!$payment) {
            return false;
        }

        return !!$payment->order_id;
    }

    public function getIsExpenseTied()
    {
        $expense = Expense::where('bank_uid', $this->bank_uid)->first();

        if (!$expense) {
            return false;
        }

        return !!$expense->expense_type_id;
    }

    public function getIsTiedAttribute()
    {
        if ($this->value > 0) {
            return $this->getIsPaymentTied();
        }

        return $this->getIsExpenseTied();
    }

    /**
     * Retorna a entrada modificada para cache do GraphQL no frontend,
     * pois após dar Update no Payment/Expense, o evento do model deleta a entrada
     */
    public static function getModEntryForCache(Entry $entry)
    {
        $entry = $entry->toArray();
        $entry['is_tied'] = true;

        return $entry;
    }
}
