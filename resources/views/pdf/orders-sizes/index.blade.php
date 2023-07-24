@extends('pdf.layout')

@section('title', 'Relatório de tamanhos')

@section('content')

@empty ($ordersSizes)
    <div class="text-center text-secondary">
        Nenhum pedido encontrado
    </div>
@else
    @foreach ($types as $garmentType => $model)
    <div @class([
        'page-break-after-always' => !$loop->last
    ])>
        @foreach ($ordersSizes[$garmentType] as $key => $orders)

            <table
            @class([
                    'page-break-after-always' => !$loop->last,
                    'page-break-inside-avoid',
                    'table table-sm table-bordered'
                    ])
            >
            @include('pdf.orders-sizes.table-head')
            @include('pdf.orders-sizes.table-body')
            </table>

        @endforeach
    </div>
    @endforeach
@endif


@endsection
