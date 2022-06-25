<?php

namespace App\GraphQL\Traits;

use App\Util\Formatter;

trait PaymentTrait
{
    public function getFormattedData(array $data)
    {
        return (new Formatter($data))
            ->currencyBRL(['value', 'price'])
            ->date('date')
            ->get();
    }

    private function errorMessages()
    {
        return [
            'value.required' => __('validation.rules.required'),
            'payment_via_id.required' => __('validation.rules.required_list', ['pronoun' => 'uma']),
            'date.required' => __('validation.rules.required', ['attribute' => 'data de pagamento']),
            'date.date_format' => __('validation.rules.date_format'),
            'date.required_if' => __('validation.rules.required'),
            'value.max_currency' => __('validation.rules.max_currency', ['subject' => 'o total restante do pedido']),
            'sponsorship_client_id.not_in' => __('validation.custom.payments.sponsorship_client_id|not_in'),
            'sponsorship_client_id.required' => __('validation.rules.required')
        ];
    }
}
