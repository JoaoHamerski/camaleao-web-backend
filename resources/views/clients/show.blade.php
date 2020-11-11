@extends('layout')

@section('title', $client->name)

@section('content')
	<div class="row mt-5">
		<div class="col-md-3">
			<a class="btn btn-outline-primary mb-2" href="{{ route('clients.index') }}">
				<i class="fas fa-arrow-circle-left fa-fw mr-1"></i>Voltar
			</a>

			@include('clients._client-card')
		</div>

		<div class="col-md-9 mt-4 mt-md-0">
			<div class="d-flex justify-content-between flex-column flex-md-row mb-2">
				@role(['atendimento', 'gerencia'])
					<a href="{{ route('orders.create', $client) }}" class="btn d-block d-md-inline btn-primary mb-2 mb-md-0">
						<i class="fas fa-plus fa-fw mr-1"></i>Novo pedido
					</a>
				@else('design')
					<span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="Você não tem permissão para isso">
						<button style="pointer-events: none;" disabled="disabled" class="btn d-block d-md-inline btn-primary mb-2 mb-md-0">
							<i class="fas fa-plus fa-fw mr-1"></i>Novo pedido
						</button>
					</span>
				@endrole

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

			<div class="card">
				<div class="card-header text-white bg-primary font-weight-bold">
					<i class="fas fa-boxes fa-fw mr-1"></i>Pedidos
				</div>

				<div class="card-body px-0">
					@include('clients.index-orders')
				</div>
			</div>

			<div class="mt-2">
				{{ $orders->links() }}
			</div>
		</div>

	</div>
@endsection