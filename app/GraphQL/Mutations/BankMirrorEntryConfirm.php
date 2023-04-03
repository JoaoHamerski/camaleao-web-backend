<?php

namespace App\GraphQL\Mutations;

use App\Models\Entry;
use App\Models\Expense;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class BankMirrorEntryConfirm
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'id' => ['required', 'exists:entries']
        ])->validate();

        $entry = Entry::find($args['id']);

        if ($entry->value > 0) {
            return $this->confirmPayment($entry);
        }

        return $this->confirmExpense($entry);
    }

    public function confirmExpense(Entry $entry)
    {
        $expense = Expense::where('bank_uid', $entry->bank_uid)->first();

        if ($expense) {
            $expense->fillConfirmation();
            $expense->save();

            return Entry::getModEntryForCache($entry);
        }

        $expense = Expense::make([
            'value' => abs($entry->value),
            'bank_uid' => $entry->bank_uid,
            'description' => $entry->description,
            'expense_via_id' => $entry->via_id,
            'date' => Carbon::createFromFormat('d/m/Y', $entry->date)->format('Y-m-d'),
            'created_at' => $entry->created_at,
        ]);

        $expense->fillConfirmation();
        $expense->saveQuietly();

        return $entry;
    }

    public function confirmPayment(Entry $entry)
    {
        $payment = Payment::where('bank_uid', $entry->bank_uid)->first();

        if ($payment) {
            $payment->fillConfirmation();
            $payment->save();

            return Entry::getModEntryForCache($entry);
        }

        $payment = Payment::make([
            'value' => $entry->value,
            'bank_uid' => $entry->bank_uid,
            'note' => $entry->description,
            'payment_via_id' => $entry->via_id,
            'date' => Carbon::createFromFormat('d/m/Y', $entry->date)->format('Y-m-d'),
            'created_at' => $entry->created_at,
        ]);

        $payment->fillConfirmation();
        $payment->saveQuietly();

        return $entry;
    }
}
