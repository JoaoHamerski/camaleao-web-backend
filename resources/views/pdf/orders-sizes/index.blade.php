@extends('pdf.layout')

@section('title', 'Relatório de tamanhos')

@section('content')

@foreach ($types as $type => $model)
    @foreach ($ordersSizes[$type] as $key => $orders)
    <div
        @class([
        'page-break-inside-avoid',
        'page-break-after-always' => !$loop->last
        ])
    >
        <table class="table table-sm table-bordered">
        @include('pdf.orders-sizes.table-head')
        @include('pdf.orders-sizes.table-body')
        </table>
    </div>
    @endforeach
@endforeach

@endsection
