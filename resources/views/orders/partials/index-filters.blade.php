<div class="d-flex justify-content-between flex-column flex-xl-row mt-2">
  <div class="col-xl-6 px-0">
    <div class="form-group table-responsive">
        <div class="input-group flex-nowrap">
          <div class="input-group-prepend">
            <a href="{{ route('orders.index') }}" 
              class="btn {{ Request::isNotFilled(['ordem', 'codigo']) ? 'btn-primary' : 'btn-outline-primary' }}">
              Prioritários
            </a>
          </div>
          
          <div class="input-group-append">
            <a href="{{ route('orders.index', ['ordem' => 'mais_antigo']) }}"
              class="btn border-left-0 {{ Request::query('ordem') == 'mais_antigo' ? 'btn-primary' : 'btn-outline-primary' }}">
                Mais antigos
            </a>

            <a href="{{ route('orders.index', ['ordem' => 'mais_recente']) }}" 
              class="btn {{ Request::query('ordem') == 'mais_recente' ? 'btn-primary' : 'btn-outline-primary' }}">
              Mais recentes
            </a>
            
            <a href="{{ route('orders.index', ['ordem' => 'data_de_entrega']) }}"
              class="btn {{ Request::query('ordem') == 'data_de_entrega' ? 'btn-primary' : 'btn-outline-primary' }}">
              Data de entrega
            </a>
          </div>
        </div>
    </div>
  </div>

  <form class="col-md-4 px-0" method="GET" action="{{ route('orders.index') }}">
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