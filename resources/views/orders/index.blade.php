@extends('layout')

@section('title', 'Pedidos')

@section('content')
    <div class="mt-5">
      @include('orders._general-report')
      @include('orders._production-date-report')
      @include('orders._general-filters')
    </div>
    
    @include('orders._filter-note')    
    <x-card 
      :header-color="[
        'warning' => Request::query('filtro') == 'pre-registro',
        'primary'
      ]"
      icon="fas fa-boxes"
      :has-body-padding="false"
      :header-url="route('orders.index')"
    >
      <x-slot name="header">Lista de todos pedidos</x-slot>

      <x-slot name="body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Cliente</th>
                <th>Código do pedido</th>
                <th>Quantidade</th>
                <th>Valor total</th>
                <th>Total pago</th>
                <th class="text-center">Produção</th>
                <th class="text-center">Entrega</th>
              </tr>
            </thead>
  
            <tbody>
              @forelse($orders as $order)
                <tr data-url="{{ $order->path() }}" class="clickable-link @if ($order->isClosed()) table-secondary @elseif ($order->isPreRegistered()) table-warning @endif">
                  <td>{{ $order->client->name }}</td>
                  <td>{{ $order->code }}</td>
                  <td>{{ $order->quantity ?? 'N/A' }}</td>
                  <td>{{ Mask::money($order->price) }}</td>
                  <td>{{ Mask::money($order->getTotalPayments()) }}</td>
                  <td class="text-center">
                    {{
                      $order->production_date 
                        ? Helper::date($order->production_date, '%d/%m/%Y')
                        : 'N/A' 
                     }}
                  </td>
                  <td class="text-center">
                    {{ 
                      $order->delivery_date 
                        ? Helper::date($order->delivery_date, '%d/%m/%Y')
                        : 'N/A' 
                      }}
                  </td>
                </tr>
              @empty
                <tr class="not-hover">
                  <td colspan="7">
                    <h5 class="text-center text-secondary mt-4">
                      @if ($orders->count())
                        Nenhum pedido com código 
                        @if (Request::filled('codigo'))<strong>"{{ Request::query('codigo') }}"</strong> @endif 
                        foi encontrado.
                      @else
                        Nenhum pedido foi encontrado
                      @endif
                    </h5>
                    <div class="small text-center">
                      <a href="{{ route('orders.index') }}">Voltar aos pedidos</a>
                    </div>
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
  
  @push('css')
    <link rel="stylesheet" href="{{ mix('css/_date-picker.css') }}">
  @endpush
  
  @push ('script')
    <script src="{{ mix('js/partials/orders/index.js') }}"></script>
    <script src="{{ mix('js/date-picker.js') }}"></script>
  @endpush
@endsection
