<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => 'O campo :attribute deve ser aceito.',
    'active_url'           => 'O campo :attribute não é uma URL válida.',
    'after'                => 'O campo :attribute deve ser uma data posterior a :date.',
    'after_or_equal'       => 'O campo :attribute deve ser uma data posterior ou igual a :date.',
    'alpha'                => 'O campo :attribute só pode conter letras.',
    'alpha_dash'           => 'O campo :attribute só pode conter letras, números e traços.',
    'alpha_num'            => 'O campo :attribute só pode conter letras e números.',
    'array'                => 'O campo :attribute deve ser uma matriz.',
    'before'               => 'O campo :attribute deve ser uma data anterior :date.',
    'before_or_equal'      => 'O campo :attribute deve ser uma data anterior ou igual a :date.',
    'between'              => [
        'numeric' => 'O campo :attribute deve ser entre :min e :max.',
        'file'    => 'O campo :attribute deve ser entre :min e :max kilobytes.',
        'string'  => 'O campo :attribute deve ser entre :min e :max caracteres.',
        'array'   => 'O campo :attribute deve ter entre :min e :max itens.',
    ],
    'boolean'              => 'O campo :attribute deve ser verdadeiro ou falso.',
    'confirmed'            => 'O campo :attribute de confirmação não confere.',
    'date'                 => 'Por favor, informe uma data válida.',
    'date_equals'          => 'O campo :attribute deve ser uma data igual a :date.',
    'date_format'          => 'O campo :attribute não corresponde ao formato :format.',
    'different'            => 'Os campos :attribute e :other devem ser diferentes.',
    'digits'               => 'O campo :attribute deve ter :digits dígitos.',
    'digits_between'       => 'O campo :attribute deve ter entre :min e :max dígitos.',
    'dimensions'           => 'O campo :attribute tem dimensões de imagem inválidas.',
    'distinct'             => 'O campo :attribute campo tem um valor duplicado.',
    'email'                => 'O campo :attribute deve ser um endereço de e-mail válido.',
    'ends_with'            => 'O campo :attribute deve terminar com um dos seguintes: :values',
    'exists'               => 'O campo :attribute selecionado é inválido.',
    'file'                 => 'O campo :attribute deve ser um arquivo.',
    'filled'               => 'O campo :attribute deve ter um valor.',
    'gt' => [
        'numeric' => 'O campo :attribute deve ser maior que :value.',
        'file'    => 'O campo :attribute deve ser maior que :value kilobytes.',
        'string'  => 'O campo :attribute deve ser maior que :value caracteres.',
        'array'   => 'O campo :attribute deve conter mais de :value itens.',
    ],
    'gte' => [
        'numeric' => 'O campo :attribute deve ser maior ou igual a :value.',
        'file'    => 'O campo :attribute deve ser maior ou igual a :value kilobytes.',
        'string'  => 'O campo :attribute deve ser maior ou igual a :value caracteres.',
        'array'   => 'O campo :attribute deve conter :value itens ou mais.',
    ],
    'image'                => 'O campo :attribute deve ser uma imagem.',
    'in'                   => 'O campo :attribute selecionado é inválido.',
    'in_array'             => 'O campo :attribute não existe em :other.',
    'integer'              => 'O campo :attribute deve ser um número inteiro.',
    'ip'                   => 'O campo :attribute deve ser um endereço de IP válido.',
    'ipv4'                 => 'O campo :attribute deve ser um endereço IPv4 válido.',
    'ipv6'                 => 'O campo :attribute deve ser um endereço IPv6 válido.',
    'json'                 => 'O campo :attribute deve ser uma string JSON válida.',
    'lt' => [
        'numeric' => 'O campo :attribute deve ser menor que :value.',
        'file'    => 'O campo :attribute deve ser menor que :value kilobytes.',
        'string'  => 'O campo :attribute deve ser menor que :value caracteres.',
        'array'   => 'O campo :attribute deve conter menos de :value itens.',
    ],
    'lte' => [
        'numeric' => 'O campo :attribute deve ser menor ou igual a :value.',
        'file'    => 'O campo :attribute deve ser menor ou igual a :value kilobytes.',
        'string'  => 'O campo :attribute deve ser menor ou igual a :value caracteres.',
        'array'   => 'O campo :attribute não deve conter mais que :value itens.',
    ],
    'max' => [
        'numeric' => 'O campo :attribute não pode ser superior a :max.',
        'file'    => 'O campo :attribute não pode ser superior a :max kilobytes.',
        'string'  => 'O campo :attribute não pode ser superior a :max caracteres.',
        'array'   => 'O campo :attribute não pode ter mais do que :max itens.',
    ],
    'max_currency' => 'O valor não pode ser maior que :max',
    'mimes'                => 'O campo :attribute deve ser um arquivo do tipo: :values.',
    'mimetypes'            => 'O campo :attribute deve ser um arquivo do tipo: :values.',
    'min' => [
        'numeric' => 'O campo :attribute deve ser pelo menos :min.',
        'file'    => 'O campo :attribute deve ter pelo menos :min kilobytes.',
        'string'  => 'O campo :attribute deve ter pelo menos :min caracteres.',
        'array'   => 'O campo :attribute deve ter pelo menos :min itens.',
    ],
    'min_currency' => 'O valor não pode ser menor que :min',
    'not_in'               => 'O campo :attribute selecionado é inválido.',
    'not_regex'            => 'O campo :attribute possui um formato inválido.',
    'numeric'              => 'O campo :attribute deve ser um número.',
    'password'             => 'A senha está incorreta.',
    'present'              => 'O campo :attribute deve estar presente.',
    'regex'                => 'O campo :attribute tem um formato inválido.',
    'required'             => 'O campo :attribute é obrigatório.',
    'required_if'          => 'O campo :attribute é obrigatório quando :other for :value.',
    'required_unless'      => 'O campo :attribute é obrigatório exceto quando :other for :values.',
    'required_with'        => 'O campo :attribute é obrigatório quando :values está presente.',
    'required_with_all'    => 'O campo :attribute é obrigatório quando :values está presente.',
    'required_without'     => 'O campo :attribute é obrigatório quando :values não está presente.',
    'required_without_all' => 'O campo :attribute é obrigatório quando nenhum dos :values estão presentes.',
    'same'                 => 'Os campos :attribute e :other devem corresponder.',
    'size'                 => [
        'numeric' => 'O campo :attribute deve ser :size.',
        'file'    => 'O campo :attribute deve ser :size kilobytes.',
        'string'  => 'O campo :attribute deve ser :size caracteres.',
        'array'   => 'O campo :attribute deve conter :size itens.',
    ],
    'starts_with'          => 'O campo :attribute deve começar com um dos seguintes valores: :values',
    'string'               => 'O campo :attribute deve ser uma string.',
    'timezone'             => 'O campo :attribute deve ser uma zona válida.',
    'unique'               => 'O campo :attribute já está sendo utilizado.',
    'uploaded'             => 'Ocorreu uma falha no upload do campo :attribute.',
    'url'                  => 'O campo :attribute tem um formato inválido.',
    'uuid' => 'O campo :attribute deve ser um UUID válido.',


    /*
    |--------------------------------------------------------------------------
    | Regras de validação utilizadas no sistema
    |--------------------------------------------------------------------------
    |
    | Essas são as strings de regras de validação chamadas manualmente
    | para validar os formulários, as regras padrões do Laravel são usadas
    | apenas como fallback.
    |
    */
    'rules' => [
        'required_list' => 'Por favor, selecione :pronoun :attribute.',
        'required' => 'Por favor, informe o campo :attribute.',
        'max_file' => 'O arquivo deve ser menor que :max.',
        'lt' => 'O campo :attribute deve ser menor que :subject.',
        'gt' => 'O campo :attribute deve ser maior que :subject.',
        'max' => 'O campo :attribute deve ser menor ou igual :subject.',
        'unique' => ':pronoun :attribute já está em uso, por favor, escolha outro.',
        'date' => 'Por favor, informe uma data válida.',
        'date_format' => 'Por favor, informe uma data válida.',
        'max_currency' => 'O campo :attribute não pode ser maior que :subject (:max).',
        'after' => 'A data informada deve ser posterior a :date.',
        'email' => 'Por favor, informe um email válido.',
        'password_confirmed' => 'A senha digitada não confere com a confirmação de senha.',
        'current_password' => 'A senha digitada não confere.'
    ],

    /*
    |--------------------------------------------------------------------------
    | Regras customizadas para formulários
    |--------------------------------------------------------------------------
    |
    | Aqui são adicionadas as regras customizadas para quando nenhuma regra de validação
    | serve para algum determinado campo.
    |
    */

    'custom' => [
        'orders' => [
            'payment_via_id|required_with' => 'Você deve selecionar uma via caso vá registrar uma entrada.',
            'price|min_currency' => 'O valor do pedido não pode ser menor que o total já pago (:min).',
            'price|required' => 'Por favor, preencha algum item para o pedido ter um preço final.'
        ],
        'payments' => [
            'payment_via_id|required' => 'Por favor, selecione uma via.',
            'client|id|required' => 'Por favor, selecione um cliente.',
        ],
        'clothing_types' => [
            'key|unique' => 'Este tipo de roupa já foi registrado.'
        ],
        'cities' => [
            'city_id|required_if' => 'Por favor, informe uma cidade caso deseje substituir a cidade deletada, ou desmarque as opções "substituir".'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'address'   => 'endereço',
        'age'       => 'idade',
        'body'      => 'conteúdo',
        'cell'      => 'celular',
        'cities_id' => 'cidade',
        'city_id' => 'cidade',
        'city'      => 'cidade',
        'client.name' => 'nome do cliente',
        'code' => 'código',
        'commission' => 'comissão',
        'country'   => 'país',
        'date'      => 'data',
        'day'       => 'dia',
        'delivery_date' => 'data de entrega',
        'description' => 'descrição',
        'discount' => 'desconto',
        'down_payment' => 'entrada',
        'excerpt'   => 'resumo',
        'expense_type_id' => 'tipo de despesa',
        'expense_via_id' => 'via',
        'first_name' => 'primeiro nome',
        'gender'    => 'gênero',
        'hour'      => 'hora',
        'last_name' => 'sobrenome',
        'message'   => 'mensagem',
        'minute'    => 'minuto',
        'mobile'    => 'celular',
        'month'     => 'mês',
        'name'      => 'nome',
        'neighborhood' => 'bairro',
        'number'    => 'número',
        'order.code' => 'código do pedido',
        'order.price' => 'valor do pedido',
        'password_confirmation' => 'confirmação da senha',
        'password'  => 'senha',
        'payment_via_id' => 'via',
        'phone'     => 'telefone',
        'print_date' => 'data de estampa',
        'seam_date' => 'data de costura',
        'second'    => 'segundo',
        'sex'       => 'sexo',
        'shipping_company_id' => 'transportadora',
        'start_date' => 'data inicial',
        'state_id' => 'estado',
        'state'     => 'estado',
        'street'    => 'rua',
        'subject'   => 'assunto',
        'text'      => 'texto',
        'time'      => 'hora',
        'title'     => 'título',
        'username'  => 'usuário',
        'value' => 'valor',
        'via_id' => 'via',
        'year'      => 'ano',
        'product_type_id' => 'produto'
    ],

];
