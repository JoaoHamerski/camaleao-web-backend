<form @if ($method == 'POST') id="formCreateOrder" @else id="formUpdateOrder" @endif>
	@if ($method == 'PATCH')
		@method('PATCH')
	@endif

	<div class="form-row d-flex flex-column flex-md-row">
		@input([
			'id' => 'name',
			'name' => 'name',
			'label' => 'Nome do pedido',
			'labelClass' => 'font-weight-bold',
			'formGroupClass' => 'col',
			'optional' => true,
			'placeholder' => 'Nome que descreva o pedido...',
			'value' => $method == 'PATCH' ? $order->name : null,
		])
	</div>

	<div class="form-row d-flex flex-column flex-md-row">
		@input([
			'id' => 'code',
			'name' => 'code',
			'label' => 'Código',
			'labelClass' => 'font-weight-bold',
			'formGroupClass' => 'col',
			'value' => $method == 'PATCH' ? $order->name : $client->getNewOrderCode(),
		])

		@input([
			'id' => 'quantity',
			'name' => 'quantity',
			'label' => 'Quantidade',
			'labelClass' => 'font-weight-bold',
			'formGroupClass' => 'col',
			'value' => $method == 'PATCH' ? $order->quantity : null,
		])
	</div>

	<div class="form-row d-flex flex-column flex-md-row">
		@input([
			'id' => 'price',
			'name' => 'price',
			'label' => 'Valor total',
			'labelClass' => 'font-weight-bold',
			'formGroupClass' => 'col',
			'value' => $method == 'PATCH' ? Mask::money($order->price) : '',
		])

		@if ($method == 'POST')
			@input([
				'id' => 'down_payment',
				'name' => 'down_payment',
				'label' => 'Entrada',
				'labelClass' => 'font-weight-bold',
				'formGroupClass' => 'col',
				'optional' => true
			])
		@endif
	</div>

	<div class="form-row d-flex flex-column flex-md-row">
		@input([
			'id' => 'production_date',
			'name' => 'production_date',
			'optional' => true,
			'formGroupClass' => 'col',
			'placeholder' => 'dd/mm/aaaa',
			'label' => 'Data de produção',
			'labelClass' => 'font-weight-bold',
			'value' => $method == 'PATCH' ? Helper::date($order->production_date, '%d/%m/%Y') : null
		])

		@input([
			'id' => 'delivery_date',
			'name' => 'delivery_date',
			'optional' => true,
			'formGroupClass' => 'col',
			'placeholder' => 'dd/mm/aaaa',
			'label' => 'Data de entrega',
			'labelClass' => 'font-weight-bold',
			'value' => $method == 'PATCH' ? Helper::date($order->delivery_date, '%d/%m/%Y') : null
		])
	</div>

	@inputFile([
		'id' => 'art_paths',
		'name' => 'art_paths[]',
		'accept' => 'image/*',
		'multiple' => true,
		'optional' => true,
		'label' => 'Imagem da arte',
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
		'label' => 'Imagem da arte',
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

	@if ($method == 'POST')
		<button class="btn btn-success" id="btnCreateOrder" >
			<i class="fas fa-check fa-fw mr-1"></i>Cadastrar
		</button>
	@else
		<button class="btn btn-success mt-3" id="btnUpdateOrder" >
			<i class="fas fa-check fa-fw mr-1"></i>Atualizar
		</button>
	@endif
</form>

@push('script')
	<script src="{{ mix('js/partials/orders/form.js') }}"></script>
@endpush