<?php

namespace App\GraphQL\Mutations;

use App\GraphQL\Traits\DailyCashTrait;
use App\Models\Entry;
use App\Models\Payment;
use Illuminate\Support\Arr;

class BankMirrorTiePayment
{
    use DailyCashTrait;
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $order = null;
        $data = $this->getFormattedData($args);
        $isNew = $this->checkForNewClientAndOrder($data);

        $this->validator($data, $isNew)->validate();

        $order = $this->getOrder($data, $isNew);

        return $this->handlePaymentTie($data, $order);
    }

    private function handlePaymentTie($data, $order = null)
    {
        $entry =  Entry::where('bank_uid', $data['bank_uid'])->first();
        $payment = Payment::where('bank_uid', $data['bank_uid'])->first();

        if (!$payment) {
            $this->createPayment($data, $order);

            return $entry;
        }

        $this->updatePayment($data, $payment, $order);

        return Entry::getModEntryForCache($entry);
    }

    public function createPayment($data, $order)
    {
        $payment = $this->getFilledPayment($data, $order);

        $payment->saveQuietly();
    }

    public function updatePayment($data, $payment, $order)
    {
        $data = array_merge($data, ['order_id' => $order->id]);

        $payment = $this->getFilledPayment($data, $order, $payment);

        $payment->save();
    }
}
