@extends('layout')

@section('title', 'Novo pedido')

@section('content')
	<div class="col-md-10 mx-auto">
		<div class="mt-5">
			<a class="btn btn-outline-primary" href="{{ $client->path() }}">
				<i class="fas fa-arrow-circle-left fa-fw mr-1"></i>Voltar
			</a>
		</div>

		<div class="card mt-3">
			<div class="card-header bg-primary text-white font-weight-bold">
				<i class="fas fa-box-open fa-fw mr-1"></i>Novo pedido para {{ $client->name }}
			</div>

			<div class="card-body">
				@include('orders._form', ['method' => 'POST'])
			</div>
		</div>
	</div>
@endsection