@extends('pdf.layout')

@section('title', "Pedido - $order->code")

@section('content')
<div class="order">
  @include('pdf.order.order-details')

  @if ($order->clothingTypes)
    <div class="page-break"></div>
    @include('pdf.order.order-clothing-types')
  @endif
</div>

@endsection
