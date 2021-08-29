@extends('layout')

@section('title', 'Novo pedido')

@section('content')
<div class="col col-lg-10 mx-auto">
    <div class="mt-5">
        <a class="btn btn-outline-primary" href="{{ $client->path() }}">
            <i class="fas fa-arrow-circle-left fa-fw mr-1"></i>Voltar
        </a>
    </div>

    <x-card class="mt-3" header-color="primary" icon="fas fa-box-open">
        <x-slot name="header">Novo pedido para {{ $client->name }}</x-slot>
        <x-slot name="body">
            <order-form :has-client="{{ $client ? 'true' : 'false' }}"></order-form>
        </x-slot>
    </x-card>
</div>
@endsection
