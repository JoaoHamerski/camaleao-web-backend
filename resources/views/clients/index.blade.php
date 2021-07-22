@extends('layout')

@section('title', 'Clientes')

@section('content')
  
  <div class="col col-md-10 mx-auto mt-5 px-0">
    <div class="d-flex justify-content-between flex-column flex-sm-row">
      <div class="mb-2 mb-sm-0">
        <span class="d-inline-block"
          @role('design')
            content="Você não tem permissão para isso"
            v-tippy="{placement: 'bottom', arrow: true, duration: 150}"
          @endrole
        >
          <button type="button" 
            @role(['atendimento', 'gerencia'])
              data-toggle="modal"
              data-target="#clientModal" 
            @else
              disabled=disabled
            @endrole
            class="btn btn-success font-weight-bold" 
          >
            <i class="fas fa-user-plus fa-fw mr-1"></i>Novo cliente
          </button>
        </span>
      </div>

      <div>
        <form method="GET" action="{{ route('clients.index') }}">
          <div class="form-group">
            <div class="input-group">
              <div class="input-group-prepend">
                <select class="custom-select" name="opcao" id="opcao">
                  <option value="nome" {{ request('opcao') == 'nome' ? 'selected="selected"' : '' }}>
                    Nome
                  </option>

                  <option value="telefone"{{ request('opcao') == 'telefone' ? 'selected="selected"' : '' }}>
                    Telefone
                  </option>

                  <option value="cidade" {{ request('opcao') == 'cidade' ? 'selected="selected"' : '' }}>
                    Cidade
                  </option>
                </select>
              </div>

              <input class="form-control" 
                name="busca" 
                type="text" 
                placeholder="Digite a busca..."
                value="{{ request('busca') }}"
              >

              <div class="input-group-append">
                <button class="btn btn-outline-primary">Buscar</button>
              </div>
            </div>
          </div>
        </form> 
      </div>
    </div>

    <x-card
      header-color="primary"
      :header-url="route('clients.index')"
      icon="fas fa-list"
      :has-body-padding="false"
    >
      <x-slot name="header">
        Lista de clientes
      </x-slot>

      <x-slot name="body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Nome</th>
                <th>Telefone</th>
                <th>Cidade</th>
              </tr>
            </thead>
        
            <tbody>
              @forelse($clients as $client)
                <tr class="clickable-link" data-url="{{ $client->path() }}">
                  <td>{!! $client->name !!}</td>
                  <td nowrap="nowrap">
                    {{ $client->phone ? Mask::phone($client->phone) : 'N/A' }}
                  </td>
                  <td nowrap="nowrap">
                    {{ $client->city->name ?? 'N/A' }}
                  </td>
                </tr>
              @empty
                <tr class="not-hover">
                  <td colspan="3">
                    <h5 class="text-center text-secondary mt-4">Nenhum cliente encontrado</h5>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </x-slot>
    </x-card>
    
    <div class="mt-2">
      {{ $clients->links() }}
    </div>
  </div>

  @role(['gerencia', 'atendimento'])
    <client-modal ref="clientModal" />
    <new-city-modal ref="newCityModal"
      @created="$refs.clientModal.$emit('city-created', $event)"
    />
  @endrole
@endsection