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
        'email' => 'O e-mail informado é inválido',
        'date_required' => 'Por favor, informe uma data.',
        'date_valid' => 'Por favor, informe uma data válida.',
        'value_required' => 'Por favor, informe um valor.',
        'file_max' => 'O arquivo armazenado deve ser menor que :max',
        'type_required' => 'Por favor, selecione um tipo',
        'via_required' => 'Por favor, selecione a via',
        'start_date_required' => 'Por favor, informe a data inicial.',
        'date_after' => 'A data informada deve ser posterior a :date',
        'password_required' => 'Por favor, digite sua senha',
        'current_password' => 'A senha digitada não confere.',
        'state_id_required' => 'Por favor, selecione um estado.',
        'commission_required' => 'Por favor, informe uma comissão.',
        'text_required' => 'Por favor, informe o texto.',
        'cities' => [
            'city_id_required_if' =>  'Por favor, informe uma cidade caso deseje substituir a cidade deletada, ou desmarque a opção "substituir".'
        ],
        'orders' => [
            'discount_lt' => 'O desconto deve ser menor que o preço total (:total_price).',
            'discount_gt' => 'O desconto deve ser maior que R$ 0,00.',
            'payment_via_id_required_with' => 'Você deve selecionar uma via caso vá registrar uma entrada.',
            'down_payment_max_currency' => 'A entrada deve ser menor que o valor final (:final_value).',
            'price_min_currency' => 'O valor do pedido não pode ser menor que o total já pago (:min).',
            'price_required' => 'Por favor, preencha algum item para o pedido ter um preço.'
        ],
        'expenses' => [
            'type' => 'Por favor, selecione um tipo.',
            'description' => 'Por favor, informe uma descrição.',
            'receipt_path_file_max' => 'O arquivo deve ser menor que :size.'
        ],
        'shipping_companies' => [
            'city_id_required' => 'Por favor, selecione uma filial.',
            'shipping_company_id_required' => 'Por favor, selecione uma transportadora.',
            'cities_id_required' => 'Por favor, selecione uma cidade.'
        ],
        'clothing_types' => [
            'key_unique' => 'Este tipo de roupa já foi registrado.'
        ],
        'commissions' => [
            'role_id_required' => 'Por favor, selecione um tipo de usuário.',
            'month_required' => 'Por favor, selecione um mês.',
            'month_date_format' => 'O campo deve ter o formato mm/aaaa.',
        ]
    ]
];
