@extends('pdf.layout')

@section('title', $title)

@section('content')
<div class="position-relative text-center mt-5">
    <div class="page-break-after-always">
        @forelse ($orders as $order)
            @foreach($order->art_paths as $imageUrl)
            <img class="img-fluid img-thumbnail w-100 mb-2" src="{{
                FileHelper::imageToBase64(
                    Helper::getPublicPathFromUrl($imageUrl)
                )
            }}">
            @endforeach
    </div>
    @empty
    <div class="img-thumbnail text-secondary py-5">
        Nenhum pedido registrado
    </div>
    @endforelse
</div>
@endsection
