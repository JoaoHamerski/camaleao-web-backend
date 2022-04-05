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

    private function errorMessages($isNewOrder = true)
    {
        return [
            'client.required' => 'Por favor, informe o nome do cliente.',
            'client.id.required' => 'Por favor, selecione um cliente.',
            'order.required' => 'Por favor, informe o código do pedido.',
            'order.unique' => 'Este código já está sendo utilizado por outro pedido.',
            'order.id.required' => 'Por favor, selecione um pedido.',
            'order.id.required_with' => 'Por favor, selecione um pedido.',
            'order_value.required' => 'Por favor, informe o valor.',
            'via_id.required' => 'Por favor, selecione uma via.',
            'value.max_currency' => $isNewOrder
                ? 'O pagamento não pode ser maior que o valor do pedido (:max).'
                : 'O pagamento não pode ser maior que o total restante (:max).',
        ];
    }
}
