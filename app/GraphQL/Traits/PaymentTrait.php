<?php

namespace App\GraphQL\Traits;

use App\Util\Formatter;

trait PaymentTrait
{
    public function getFormattedData(array $data)
    {
        if ($data['is_sponsor']) {
            $data['use_client_balance'] = false;
            $data['use_client_bonus'] = false;
        }

        if ($data['use_client_balance'] || $data['use_client_bonus']) {
            $data['value'] = 0;
        }

        return (new Formatter($data))
            ->currencyBRL(['value', 'price', 'credit', 'bonus'])
            ->date('date')
            ->get();
    }

    private function errorMessages()
    {
        return [
            'credit.required_if' => __('validation.rules.required'),
            'bank_uid.unique' => __('validation.custom.payments.unique'),
            'before_or_equal' => __('validation.rules.before_or_equal_today'),
            'value.required' => __('validation.rules.required'),
            'payment_via_id.required' => __('validation.rules.required_list', ['pronoun' => 'uma']),
            'date.required' => __('validation.rules.required', ['attribute' => 'data de pagamento']),
            'date.date_format' => __('validation.rules.date_format'),
            'date.required_if' => __('validation.rules.required'),
            'value.max_currency' => __('validation.rules.max_currency', ['subject' => 'o total restante do pedido']),
            'sponsorship_client_id.not_in' => __('validation.custom.payments.sponsorship_client_id|not_in'),
            'sponsorship_client_id.required_if' => __('validation.rules.required_list')
        ];
    }
}
