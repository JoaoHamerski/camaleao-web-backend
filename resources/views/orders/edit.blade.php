@extends('layout')

@section('title', 'Editar pedido - ' . $order->code)

@section('content')

	<div class="row mt-5">
		<div class="col-md-3">
			<a href="{{ $order->path() }}" class="btn btn-outline-primary">
				<i class="fas fa-arrow-circle-left fa-fw mr-1"></i>Voltar
			</a>	
		</div>

	</div>

	<div class="row mt-2">
		<div class="col-md-3 mb-3">
			@include('clients._client-card')
		</div>

		<div class="col-md-9">
			<x-card
				header-color="primary"
				icon="fas fa-edit"
			>
				<x-slot name="header">Alterar dados do pedido</x-slot>
				<x-slot name="body">
					<order-form 
						:is-edit="true"
						order-code="{{ $order->code }}"
						client-id="{{ $order->client->id }}"
					/>
				</x-slot>
			</x-card>
		</div>
	</div>
@endsection