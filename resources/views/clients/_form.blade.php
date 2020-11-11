<form>
  @input([
    'id' => 'name',
    'name' => 'name',
    'placeholder' => 'Digite o nome do cliente...',
    'value' => isset($method) && $method == 'PATCH' ? $client->name : '',
    'label' => 'Nome: ',
    'labelClass' => 'font-weight-bold'
  ])

  @input([
    'id' => 'phone',
    'name' => 'phone',
    'placeholder' => 'Digite o telefone...',
    'value' => isset($method) && $method == 'PATCH' ? $client->phone : '',
    'label' => 'Telefone: ',
    'labelClass' => 'font-weight-bold'
  ])

  @input([
    'id' => 'city',
    'name' => 'city',
    'placeholder' => 'Digite a cidade...',
    'value' => isset($method) && $method == 'PATCH' ? $client->city : '',
    'label' => 'Cidade: ',
    'labelClass' => 'font-weight-bold',
    'attributes' => [
      'list' => 'cities',
      'autocomplete' => 'no'
    ]
  ])

  @dataList([
    'id' => 'cities',
    'items' => $cities
  ])

  <div class="mt-1">
    @if (isset($method) && $method == 'PATCH')
      <button class="btn btn-outline-success" id="btnEditClient">
        Atualizar
      </button>
    @else
      <button class="btn btn-outline-success" id="btnCreateClient">
        <i class="fas fa-check fa-fw mr-1"></i>Cadastrar
      </button>
    @endif

    <button class="btn btn-light" data-dismiss="modal">Cancelar</button>
  </div>
</form>

@push('script')
  <script src="{{ mix('js/partials/client-form.js') }}"></script>
@endpush