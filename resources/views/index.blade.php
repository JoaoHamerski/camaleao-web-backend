@extends('layout')

@section('title', 'Lista de clientes')

@section('content')
	
	<div class="col-md-10 mx-auto mt-5">
		<div class="d-flex justify-content-between">
			<div>
				<button type="button" data-toggle="modal" data-target="#clientCreateModal" class="btn btn-success">
					<i class="fas fa-user-plus fa-fw mr-1"></i>Novo cliente
				</button>	
			</div>

			<div>
				<form method="GET" action="{{ route('clients.index') }}">
					<div class="form-group">
						<div class="input-group">
							<input class="form-control" name="nome" type="text" placeholder="Por nome...">
							<div class="input-group-append">
								<button class="btn btn-outline-primary">Buscar</button>
							</div>
						</div>
					</div>
				</form>	
			</div>
		</div>

		<div class="card mt-2">
			<div class="card-header bg-primary font-weight-bold text-white">
				<i class="fas fa-list fa-fw mr-1"></i> Lista de clientes
			</div>

			<div class="card-body px-0">
				<table class="table table-hover">
					<thead>
						<tr>
							<th>Nome</th>
							<th>Telefone</th>
							<th>Cidade</th>
						</tr>
					</thead>

					<tbody>
						@foreach($clients as $client)
							<tr class="clickable-link" 
								onclick="window.location = '{{ route('clients.show', $client->id) }}'">
								<td>{{ $client->name }}</td>
								<td>{{ $client->phone ? Mask::phone($client->phone) : '[não informado]' }}</td>
								<td>{{ $client->city ?? '[não informado]' }}</td>
							</tr>
						@endforeach
					</tbody>	
				</table>	

			</div>
		</div>
		
		<div class="mt-2">
			{{ $clients->links() }}
		</div>
	</div>

	@include('clients.create-modal')
@endsection