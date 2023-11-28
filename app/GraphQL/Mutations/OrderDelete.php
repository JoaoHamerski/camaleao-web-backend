<?php

namespace App\GraphQL\Mutations;

use App\GraphQL\Exceptions\UnprocessableException;
use App\Models\Order;

class OrderDelete
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $order = Order::with('bonus')->find($args['id']);

        if (!$order) {
            throw new UnprocessableException(
                'Erro ao deletar',
                'O pedido especificado não existe.'
            );
        }

        $order->delete();

        return $order;
    }
}
