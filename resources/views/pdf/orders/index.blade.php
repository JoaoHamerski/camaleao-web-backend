@extends('pdf.layout')

@push('styles')
<style>
  .td-image {
    max-height: 120px;
    border: 4px solid white;
    border-radius: 25%;
    outline: 1px solid #6c757d2c;
  }

  .table tr:nth-child(even) td {
    background-color: #6c757d27;
  }
  .table tr:nth-child(odd) td {
    background-color: #a1a3a518;
  }
</style>
@endpush

@section('title', 'Relatório geral de pedidos')

@section('content')

@includeWhen(
  $filters['display_filter_info'],
  'pdf.orders.orders-filter-info'
)

@if (Helper::filled($filters, 'closed_at') && $filters['state'] === 'open')
<div class="text-center text-secondary mt-5">
  Você está filtrando pedidos <strong>Em aberto</strong> pela <strong>Data de fechamento</strong>, isso parece genial...
</div>
@endif

@forelse($orders as $order)
<table class="table page-break-inside-avoid">
  <tbody>
    <tr>
      <td rowspan="{{ $rowSpanCalc($order) }}" class="bg-secondary td-ignore-striped text-center w-40">
        @if ($order->art_paths)
        <img class="td-image" src="{{
          FileHelper::imageToBase64(
            Helper::getPublicPathFromUrl($order->art_paths[0])
            )
          }}"
        >
        @else
        <div class="text-center text-secondary">
          [SEM IMAGEM]
        </div>
        @endif
      </td>

      <td>
        <b>Cliente: </b> {{ $order->client->name }}
      </td>
    </tr>

    <tr>
      <td>
        <b>Código: </b> {{ $order->code }}
      </td>
    </tr>

    <tr>
      <td>
        <b>Quantidade: </b> {{ $order->quantity }}
      </td>
    </tr>

    @if ($order->closed_at)
    <tr>
      <td>
        <b>Fechado em: </b> {{ Mask::date($order->closed_at) }}
      </td>
    </tr>
    @endif

    @if ($order->delivery_date)
    <tr>
      <td>
        <b>Data de entrega: </b> {{ Mask::date($order->delivery_date)}}
      </td>
    </tr>
    @endif

  </tbody>
</table>
@empty
  @include('pdf.empty')
@endforelse

@endsection
