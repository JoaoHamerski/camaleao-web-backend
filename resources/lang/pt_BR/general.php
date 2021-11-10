<?php

/*
    |--------------------------------------------------------------------------
    | Mensagens Gerais
    |--------------------------------------------------------------------------
    |
    | Mensagens gerais que são reutilizadas durante toda a aplicação
    | e em outros arquivos de i18n.
    |
    */
return [
    'validation' => [
        'date_required' => 'Por favor, informe uma data.',
        'date_valid' => 'Por favor, informe uma data válida.',
        'value_required' => 'Por favor, informe um valor.',
        'file_max' => 'O arquivo armazenado deve ser menor que :max',
        'orders' => [
            'discount_lt' => 'O desconto deve ser menor que o preço total (:total_price).',
            'discount_gt' => 'O desconto deve ser maior que R$ 0,00.',
            'payment_via_id_required_with' => 'Você deve selecionar uma via caso vá registrar uma entrada.',
            'down_payment_max_currency' => 'A entrada deve ser menor que o valor final (:final_value).',
            'price_min_currency' => 'O valor do pedido não pode ser menor que o total já pago (:min).',
            'price_required' => 'Por favor, preencha algum item para o pedido ter um preço.'
        ]
    ]
];
