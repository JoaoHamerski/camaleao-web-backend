@extends('layout')

@section('title', $client->name)

@section('content')
  <div class="row mt-5">
    <div class="col-md-3">
      <a class="btn btn-outline-primary mb-2" href="{{ route('clients.index') }}">
        <i class="fas fa-arrow-circle-left fa-fw mr-1"></i>Voltar
      </a>

      @include('clients._client-card')
    </div>

    <div class="col-md-9 mt-4 mt-md-0">
      <div class="d-flex justify-content-between flex-column flex-md-row mb-2">
        <span class="d-block mb-2 mb-md-0"
          @role('design')
            content="Você não tem permissão para isso"
            v-tippy="{arrow: true, placement: 'bottom',  duration: 150}"
          @endrole
        >
          <a
            @class([
              'btn btn-primary',
              'disabled' => Auth::user()->hasRole('design')
            ])

            @role(['gerencia', 'atendimento'])
              href="{{ route('orders.create', $client) }}"
            @endrole
          >
            <i class="fas fa-plus fa-fw mr-1"></i>Novo pedido
          </a>
        </span>

        <div>
          <form action="{{ route('clients.show', $client) }}" method="GET">
            <div class="input-group">
              <input type="text" 
                name="codigo" 
                class="form-control" 
                placeholder="Buscar por código"
                @if(Request::has('codigo')) value="{{ Request::query('codigo') }}" @endif
              >

              <div class="input-group-append">
                <button class="btn btn-outline-primary" type="submit">Buscar</button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <x-card
        header-color="primary"
        icon="fas fa-boxes"
        :has-body-padding="false"
      >
        <x-slot name="header">Pedidos</x-slot>

        <x-slot name="body">
          <div class="table-responsive">
            <table class="table table-hover">
              @if ($orders->count())
                <thead>
                  <tr>
                    <th>Código</th>
                    <th>Valor total</th>
                    <th>Total pago</th>
                    <th>Quantidade</th>
                    <th>Data de produção</th>
                    <th>Data de entrega</th>
                  </tr>
                </thead>
              @endif
  
              <tbody>
                @forelse ($orders as $order)
                  <tr data-url="{{ $order->path() }}"
                    @class([
                      'clickable-link',
                      'table-secondary' => $order->isClosed(),
                      'table-warning' => $order->isPreRegistered()
                    ])
                    data-id="{{ $order->id }}"
                  >
                    <td>{{ $order->code }}</td>
                    <td>{{ Mask::money($order->price) }}</td>
                    <td>{{ Mask::money($order->getTotalPayments()) }}</td>
                    <td>{{ $order->quantity ?? 'N/A'}}</td>
                    <td>
                      {{
                        $order->production_date
                          ? Helper::date($order->production_date, '%d/%m/%Y')
                          : 'N/A'
                      }}
                    </td>
                    <td>
                      {{
                        $order->delivery_date
                          ? Helper::date($order->delivery_date, '%d/%m/%Y')
                          : 'N/A'
                      }}
                    </td>
                  </tr>
                @empty
                  <tr class="not-hover">
                    <td colspan="6">
                      <h5 class="text-center text-secondary mt-3">
                      @if ($client->orders->count() && Request::filled('codigo'))
                        Nenhum pedido com o código <strong>"{{ Request::query('codigo') }}"</strong> foi encontrado.
                      @else
                        Nenhum pedido foi cadastrado para este cliente ainda.
                      @endif
                      </h5>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </x-slot>
      </x-card>

      <div class="mt-2">
        {{ $orders->links() }}
      </div>
    </div>

  </div>
@endsection