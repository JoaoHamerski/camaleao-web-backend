@extends('pdf.layout')

@section('title', 'Relatório de tamanhos')

@section('content')

@empty ($ordersSizes)
    <div class="text-center text-secondary">
        Nenhum pedido encontrado
    </div>
@else
    @foreach ($types as $garmentType => $model)
        @foreach ($ordersSizes[$garmentType] as $key => $orders)

        <table
            @class([
                'page-break-after-always' => !$loop->last,
                'page-break-inside-avoid' => !$loop->first,
                'table table-sm table-bordered'
            ])
        >
        @include('pdf.orders-sizes.table-head')
        @include('pdf.orders-sizes.table-body')
        </table>

        @endforeach
    @endforeach
@endif


@endsection
