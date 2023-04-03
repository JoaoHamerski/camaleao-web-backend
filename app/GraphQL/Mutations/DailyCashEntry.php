<?php

namespace App\GraphQL\Mutations;

use App\Models\Order;
use App\Traits\EntriesTrait;
use App\GraphQL\Traits\DailyCashTrait;
use Illuminate\Validation\ValidationException;

class DailyCashEntry
{
    use EntriesTrait, DailyCashTrait;

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $order = null;
        $data = $this->getFormattedData($args);
        $isNew = $this->checkForNewClientAndOrder($data);

        if ($data['untied']) {
            $this->validator($data, $isNew)->validate();

            return $this->createEntry($data);
        }

        $order = $this->getOrder($data, $isNew);

        return $this->createPayment($data, $order);
    }

    public function createPayment(array $data, Order $order = null)
    {
        $payment = $this->getFilledPayment($data, $order);

        if ($this->isFromEntries($data) && !$this->isValidEntry($data)) {
            throw ValidationException::withMessages([
                'value' => 'Entrada detectada como duplicada.'
            ]);
        }

        if ($payment->isConfirmable($data)) {
            $payment->fillConfirmation();
        }

        if ($payment->sponsorship_client_id) {
            $payment->date = null;
        }

        $payment->save();

        return $payment;
    }
}
