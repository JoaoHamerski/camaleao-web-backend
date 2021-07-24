<x-card
	header-color="success"
	icon="fas fa-user"
>
	<x-slot name="header">Cliente</x-slot>
	<x-slot name="body">

		<div class="text-secondary font-weight-bold">Nome: </div>
		<div>{{ $client->name }}</div>
		
		<hr>
		
		<div class="text-secondary font-weight-bold">Telefone/Celular: </div>
		<div>{{ $client->phone ? Mask::phone($client->phone) : 'N/A' }}</div>
		
		<hr>
		
		<div class="text-secondary font-weight-bold">Cidade:</div>
		<div>
			{{ $client->city->name ?? 'N/A' }}
			{{  $client->stateAbbr ? ' - ' . $client->stateAbbr : '' }}
		</div>
		
		<hr>
		
		<div class="text-secondary font-weight-bold">Filial: </div>
		<div>
			{{ $client->branchCityName ? $client->branchCityname : 'N/A' }}
			{{ $client->branchStateAbbr ? ' - ' . $client->branchStateAbbr : '' }}
		</div>
		
		<hr>
		
		<div class="text-secondary font-weight-bold">Transportadora: </div>
		<div>
			{{ $client->shippingCompany ? $client->shippingCompany->name : 'N/A' }}
		</div>
		
		<hr>
		
		<div class="@if ($client->getTotalOwing() > 0) text-danger @else text-success @endif font-weight-bold">Total devendo: </div>
		
		<div class="@if($client->getTotalOwing() > 0) text-danger @else text-success @endif">{!! Mask::money($client->getTotalOwing(), 2) !!}</div>
	</x-slot>

	@if (Request::routeIs('clients.show'))
		<x-slot name="footer">
			<div class="d-flex flex-column">
				<a class="mb-2" href="" data-target="#clientModal" data-toggle="modal">
					<i class="fas fa-user-edit fa-fw mr-1"></i>Editar dados
				</a>

				<a class="text-danger" 
					href="#clientDeleteModal" 
					data-toggle="modal"
				>
					<i class="fas fa-trash-alt fa-fw mr-1"></i>Deletar cliente
				</a>
			</div>
		</x-slot>
	@endif
</x-card>

@if (Request::routeIs('clients.show'))
<client-modal 
	:is-edit="true" 
	:id="{{ $client->id}}" 
	ref="clientModal"
></client-modal>

<client-delete-modal id="{{ $client->id }}"></client-delete-modal>
<new-city-modal 
	ref="newCityModal" 
	@created="$refs.clientModal.$emit('city-created', $event)"
></new-city-modal>
@endif