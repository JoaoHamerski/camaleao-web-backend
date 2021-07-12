@extends('layout')

@section('title', 'Editar pedido - ' . $order->code)

@section('content')

	<div class="row mt-5">
		<div class="col-md-3">
			<a href="{{ $order->path() }}" class="btn btn-outline-primary">
				<i class="fas fa-arrow-circle-left fa-fw mr-1"></i>Voltar
			</a>	
		</div>

		<div class="col-md-9">
			
		</div>
	</div>

	<div class="row mt-2">
		<div class="col-md-3 mb-3">
			@include('clients._client-card')
		</div>

		<div class="col-md-9">
			<div class="card">
				<div class="card-header bg-primary text-white font-weight-bold">
					<i class="fas fa-edit fa-fw mr-1"></i>Alterar dados do pedido
				</div>

				<div class="card-body">
					<order-form 
						:is-edit="true"
						order-code="{{ $order->code }}"
						client-id="{{ $order->client->id }}"
					></order-form>
				</div>
			</div>
		</div>
	</div>
@endsection