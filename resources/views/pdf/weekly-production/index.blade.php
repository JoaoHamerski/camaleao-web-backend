@extends('pdf.layout')

@section('title', $title)

@section('content')
<div class="position-relative text-center mt-5">
    <div class="page-break-after-always">
    @forelse ($orders as $order)
        @if ($order->art_paths)
            @foreach($order->art_paths as $imageUrl)
            <img class="img-fluid img-thumbnail w-100 mb-2" src="{{
                FileHelper::imageToBase64(
                    Helper::getPublicPathFromUrl($imageUrl)
                )
            }}">
            @endforeach
        @else
            <div class="img-thumbnail py-5 fw-bold text-secondary">
                PEDIDO SEM IMAGEM DE TAMANHOS
                <div class="text-uppercase">
                    @if ($order->code) {{ $order->code }} - @endif
                    {{ Helper::plural($order->quantity, 'f', 'peça') }}
                </div>
            </div>
        @endif
    </div>
    @empty
    <div class="img-thumbnail text-secondary py-5">
        Nenhum pedido registrado
    </div>
    @endforelse
</div>
@endsection
