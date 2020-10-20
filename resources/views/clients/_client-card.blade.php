<div class="card">
	<div class="card-header font-weight-bold bg-success text-white">
		<i class="fas fa-user fa-fw mr-1"></i>Cliente
	</div>

	<div class="card-body">
		<div class="text-secondary font-weight-bold">Nome: </div>
		<div>{{ $client->name }}</div>

		<hr>

		<div class="text-secondary font-weight-bold">Telefone/Celular: </div>
		<div>{{ $client->phone ? Mask::phone($client->phone) : '[não informado]' }}</div>

		<hr>

		<div class="text-secondary font-weight-bold">Cidade:</div>
		<div>{{ $client->city ?? '[não informado]' }}</div>

		<hr>

		<div class="@if ($client->getTotalOwing() > 0) text-danger @else text-success @endif font-weight-bold">Total devendo: </div>

		<div class="@if($client->getTotalOwing() > 0) text-danger @else text-success @endif">{!! Mask::money($client->getTotalOwing(), 2) !!}</div>
	</div>

	@if (Request::routeIs('clients.show'))
		@role(['atendimento', 'gerencia'])
			<div class="card-footer">
				<div class="d-flex flex-column">
					<a class="mb-2" href="" data-target="#clientEditModal" data-toggle="modal">
						<i class="fas fa-user-edit fa-fw mr-1"></i>Editar dados
					</a>

					<a class="text-danger" id="btnDeleteClient" href="">
						<i class="fas fa-trash-alt fa-fw mr-1"></i>Deletar cliente
					</a>
				</div>
			</div>
			@include('clients.edit-modal')
		@endrole

	@endif
</div>