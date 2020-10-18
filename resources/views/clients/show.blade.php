@extends('layout')

@section('title', $client->name)

@section('content')
	<div class="row mt-5">
		<div class="col-md-3">
			<a class="btn btn-outline-primary" href="{{ route('clients.index') }}">
				<i class="fas fa-arrow-circle-left fa-fw mr-1"></i>Voltar
			</a>	
		</div>	

		<div class="col-md-9 d-flex flex-row justify-content-between">
			<div class="d-flex justify-content-between">
				<a href="{{ route('orders.create', $client) }}" class="btn btn-primary">
					<i class="fas fa-plus fa-fw mr-1"></i>Novo pedido
				</a>
			</div>

			<div>
				<form action="{{ route('clients.show', $client) }}" method="GET">
					<div class="input-group">
						<input type="text" 
							name="codigo" 
							class="form-control" 
							placeholder="Buscar por código"
							@if(Request::has('codigo')) value="{{ Request::query('codigo') }}" @endif>

						<div class="input-group-append">
							<button class="btn btn-outline-primary" type="submit">Buscar</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>

	<div class="row mt-2">
		<div class="col-md-3">
			@include('clients._client-card')
		</div>

		<div class="col-md-9">
			<div class="card">
				<div class="card-header text-white bg-primary font-weight-bold">
					<i class="fas fa-boxes fa-fw mr-1"></i>Pedidos
				</div>

				<div class="card-body px-0">
					@include('orders.index-table')
				</div>
			</div>
		</div>

		{{ $orders->links() }}
	</div>
@endsection