<form @if ($method == 'POST') id="formCreateOrder" @else id="formUpdateOrder" @endif>
	@if ($method == 'PATCH')
		@method('PATCH')
	@endif



	<h5 class="font-weight-bold text-secondary mt-3">Anexos</h5>
	@inputFile([
		'id' => 'art_paths',
		'name' => 'art_paths[]',
		'accept' => 'image/*',
		'multiple' => true,
		'optional' => true,
		'label' => 'Imagens da arte',
		'labelClass' => 'font-weight-bold',
		'fileLabel' => $method == 'PATCH' ? 'Adicionar mais arquivos' : 'Escolher arquivos'
	])

	@if ($method == 'PATCH')
		<div class="row mb-3">
			@foreach($order->getPaths('art_paths') as $path)
				<div class="col-md-3 mb-3">
					<div class="position-relative img-wrapper">
						<a target="_blank" class="stretched-link" href="{{ $path }}"></a>
						<img class="img-thumbnail img-fluid" src="{{ $path }}" alt="">
						<div class="btn-delete-image">X</div>
					</div>
				</div>
			@endforeach
		</div>
	@endif

	@inputFile([
		'id' => 'size_paths',
		'name' => 'size_paths[]',
		'accept' => 'image/*',
		'multiple' => true,
		'optional' => true,
		'label' => 'Imagens do tamanho',
		'labelClass' => 'font-weight-bold',
		'fileLabel' => $method == 'PATCH' ? 'Adicionar mais arquivos' : 'Escolher arquivos'
	])

	@if ($method == 'PATCH')
		<div class="row mb-3">
			@foreach($order->getPaths('size_paths') as $path)
				<div class="col-md-3 mb-3">
					<div class="position-relative img-wrapper">
						<a target="_blank" class="stretched-link" href="{{ $path }}"></a>
						<img class="img-thumbnail img-fluid" src="{{ $path }}" alt="">
						<div class="btn-delete-image">X</div>
					</div>
				</div>
			@endforeach
		</div>
	@endif

	@inputFile([
		'id' => 'payment_voucher_paths',
		'name' => 'payment_voucher_paths[]',
		'accept' => 'image/*,.pdf',
		'multiple' => true,
		'optional' => true,
		'label' => 'Comprovantes de pagamento',
		'labelClass' => 'font-weight-bold',
		'fileLabel' => $method == 'PATCH' ? 'Adicionar mais arquivos' : 'Escolher arquivos'
	])

	@if ($method == 'PATCH')
		<ul class="list-group">
			@foreach($order->getPaths('payment_voucher_paths') as $path)
				<li class="list-group-item d-flex justify-content-between">
					<a target="_blank" href="{{ $path }}">
						Comprovante {{ $loop->index + 1 }}.{{ Helper::getExtension($path) }}
					</a>
					<a class="text-danger ml-4 btn-delete-payment-voucher" href="">Deletar</a>
				</li>
			@endforeach	
		</ul>
	@endif

	<div class="d-flex justify-content-between">
		@if ($method == 'POST')
			<button class="btn btn-success" id="btnCreateOrder" >
				<i class="fas fa-check fa-fw mr-1"></i>Cadastrar
			</button>
		@else
			<button class="btn btn-success mt-3" id="btnUpdateOrder" >
				<i class="fas fa-check fa-fw mr-1"></i>Atualizar
			</button>
		@endif

		<a class="btn btn-light" href="{{ route('clients.show', $client) }}">Cancelar</a>
	</div>
</form>

@push('script')
	<script src="{{ mix('js/partials/orders/form.js') }}"></script>
@endpush