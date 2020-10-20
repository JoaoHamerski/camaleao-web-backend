<form>
  
  <div class="form-group">
    <label class="font-weight-bold" for="name">Nome: </label>
    <input name="name" 
        list="names" 
        id="name" 
        type="text" 
        class="form-control"
        autocomplete="new-name" 
        value="{{ isset($method) && $method == 'PATCH' ? $client->name : '' }}">

      <datalist id="names">
        @foreach($clientNames as $name)
          <option value="{{ $name }}"></option>
        @endforeach
      </datalist>
  </div>

  <div class="form-group">
    <label class="font-weight-bold" for="phone">Telefone: </label>
    <input name="phone" 
        id="phone" 
        type="text" 
        class="form-control"
        value="{{ isset($method) && $method== 'PATCH' ? $client->phone : '' }}">
  </div>

  <div class="form-group">
    <label class="font-weight-bold" for="city">Cidade: </label>
    <input name="city" 
        list="cities" 
        id="city" 
        type="text" 
        class="form-control"
        autocomplete="no" 
        value="{{ isset($method) && $method == 'PATCH' ? $client->city : '' }}">
      <datalist id="cities">
        @foreach($cities as $city)
          <option value="{{ $city }}"></option>
        @endforeach
      </datalist>
  </div>

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