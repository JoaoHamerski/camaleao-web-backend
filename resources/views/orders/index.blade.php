@extends('layout')

@section('title', 'Pedidos')

@section('content')
    <div class="mt-5">
      @include('orders._general-report')

      @include('orders._production-date-report')

      @include('orders._general-filters')
    
    </div>
    
    <div>
      <small class="text-secondary">
        @if (Request::query('ordem') == 'mais_antigo')
          Exibindo pedidos por ordem de cadastro mais antigos primeiros, incluindo pedidos fechados.
        @elseif (Request::query('ordem') == 'mais_recente')
          Exibindo pedidos por ordem de cadastro mais recente primeiros, incluindo pedidos fechados.
        @elseif (Request::query('ordem') == 'data_de_entrega')
          Exibindo pedidos por ordem de data de entrega mais antiga primeiro, apenas pedidos em aberto
          <br>
          (pedidos sem data de entrega informada ficam por último).
        @elseif (Request::query('filtro') == 'pre-registro')
          <span class="text-danger font-weight-bold">Pedidos que precisam ter seus dados completados.</span>
        @else 
          Exibindo pedidos por ordem de cadastro mais antigo primeiros, apenas pedidos em aberto.
        @endif
      </small>
    </div>  

  <div class="card">
    <div class="card-header @if(Request::query('filtro') == 'pre-registro') bg-warning @else bg-primary @endif font-weight-bold text-white position-relative">
      <a href="{{ route('orders.index') }}" class="stretched-link"></a>
      <i class="fas fa-boxes fa-fw mr-1"></i>Lista de todos pedidos
    </div>

    <div class="card-body px-0">
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
    </div>
  </div>

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
