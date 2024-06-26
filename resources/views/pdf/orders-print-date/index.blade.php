@extends('pdf.layout')

@section('title', $title ?? '')

@section('content')
@forelse ($orders as $order)
<div class="page-break-after-always mb-4">
  <table class="table page-break-inside-avoid">
    <thead>
      <tr class="bg-primary text-white">
        <th>CÓDIGO</th>
        <th>CLIENTE</th>
        <th>PEÇAS</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="bg-secondary w-25">{{ $order->code ?? 'N/A' }}</td>
        <td class="bg-secondary">{{ $order->client ? $order->client->name : 'N/A' }}</td>
        <td class="bg-secondary w-25 text-center">{{ $order->quantity ?? 'N/A' }}</td>
      </tr>
    </tbody>
  </table>

  @include('pdf.orders-print-date.order-images')
</div>
@empty
  @include('pdf.empty')
@endforelse
@endsection
