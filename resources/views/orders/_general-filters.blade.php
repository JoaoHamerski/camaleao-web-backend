<div class="mt-2">
  <div class="form-group table-responsive">
    <div class="input-group flex-nowrap">
      <div class="input-group-prepend">
        <a href="{{ route('orders.index') }}"
          @class([
            'btn btn-outline-primary',
            'active' => Request::isNotFilled(['ordem', 'codigo', 'filtro'])
          ])
        >
          Prioritários
        </a>
      </div>

      <div class="input-group-append">
        <a href="{{ route('orders.index', ['ordem' => 'mais_antigo']) }}"
          @class([
            'btn btn-outline-primary border-left-0',
            'active' => Request::query('ordem') === 'mais_antigo'
          ])
        >
          Mais antigos
        </a>

        <a href="{{ route('orders.index', ['ordem' => 'mais_recente']) }}"
          @class([
            'btn btn-outline-primary',
            'active' => Request::query('ordem') === 'mais_recente'
          ])
        >
          Mais recentes
        </a>

        <a href="{{ route('orders.index', ['ordem' => 'data_de_entrega']) }}"
          @class([
            'btn btn-outline-primary',
            'active' => Request::query('ordem') === 'data_de_entrega'
          ])
        >
          Data de entrega
        </a>

        <a href="{{ route('orders.index', ['filtro' => 'pre-registro']) }}"
          @class([
            'btn btn-outline-primary',
            'active' => Request::query('filtro') === 'pre-registro'
          ])
        >
          Pré-registro
          <span
            @class([
              'badge badge-pill',
              'badge-light' => Request::query('filtro') === 'pre-registro',
              'badge-primary' => Request::query('filtro') !== 'pre-registro'
            ])
          >{{ $preRegisteredCount }}</span>
        </a>
      </div>
    </div>
  </div>

  <form class="col-md-5 px-0" method="GET" action="{{ route('orders.index') }}">
    @input([
      'name' => 'codigo',
      'placeholder' => 'Por código...',
      'value' => Request::query('codigo'),
      'inputGroup' => [
        'btnAppend' => [
          'class' => 'btn btn-outline-primary',
          'attributes' => ['type' => 'submit'],
          'text' => 'Buscar'
        ]
      ]
    ])
  </form>
</div>