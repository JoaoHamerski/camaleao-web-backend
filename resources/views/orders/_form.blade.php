<form @if ($method == 'POST') id="formCreateOrder" @else id="formUpdateOrder" @endif>
	@if ($method == 'PATCH')
		@method('PATCH')
	@endif

	<div class="form-row d-flex flex-column flex-md-row">
		<div class="form-group col">
			<label class="font-weight-bold" for="name">Nome do pedido</label>
			<small class="text-secondary">(opcional)</small>	
			<input class="form-control" 
				type="text"
				id="name"
				name="name"
				placeholder="Nome que descreva o pedido..." 
				@if ($method == 'PATCH')
				value="{{ $order->name }}"
				@endif>
		</div>
	</div>

	<div class="form-row d-flex flex-column flex-md-row">

		<div class="form-group col">
			<label class="font-weight-bold" for="code">Código </label>
			<input class="form-control" 
				type="text" 
				id="code" 
				name="code"
				@if ($method == 'PATCH') 
				value="{{ $order->code }}" 
				@else 
				value="{{ $client->getNewOrderCode() }}"
				@endif>
		</div>
		<div class="form-group col">
			<label class="font-weight-bold" for="quantity">Quantidade </label>
			<input class="form-control" 
				type="text" 
				id="quantity" 
				name="quantity"
				@if ($method == 'PATCH') value="{{ $order->quantity }}" @endif>
		</div>

	</div>

	<div class="form-row d-flex flex-column flex-md-row">
		<div class="form-group col">
			<label class="font-weight-bold" for="price">Valor total</label>
			<input class="form-control" 
				type="text" 
				id="price" 
				name="price"
				@if ($method == 'PATCH') value="{{ Mask::money($order->price) }}" @endif>
		</div>

		@if ($method == 'POST')
			<div class="form-group col">
				<label for="down_payment">
					<span class="font-weight-bold">Entrada</span> <small class="text-secondary">(opcional)</small> 
				</label>
				<input class="form-control" type="text" id="down_payment" name="down_payment">
			</div>
		@endif
	</div>

	<div class="form-row d-flex flex-column flex-md-row">

		<div class="form-group col">
			<label class="font-weight-bold" for="production_date">Data de produção</label>
			<small class="text-secondary">(opcional)</small>
			<input class="form-control" 
				type="text" 
				id="production_date" 
				name="production_date" 
				placeholder="dd/mm/aaaa"
				@if ($method == 'PATCH') value="{{ Helper::date($order->production_date, '%d/%m/%Y') }}" @endif>
		</div>
		<div class="form-group col">
			<label class="font-weight-bold" for="delivery_date">Data de entrega </label>
			<small class="text-secondary">(opcional)</small>
			<input class="form-control" 
				type="text" 
				id="delivery_date" 
				name="delivery_date" 
				placeholder="dd/mm/aaaa"
				@if ($method == 'PATCH') value="{{ Helper::date($order->delivery_date, '%d/%m/%Y') }}" @endif>
		</div>
	</div>

	<div class="form-group">
		<label class="font-weight-bold">Imagem da arte </label>
		<small>(opcional)</small>
		<div class="custom-file">
			<input type="file" value="@old('art_paths')" name="art_paths[]" accept="image/*" class="custom-file-input" multiple="multiple">
			@if ($method == 'PATCH')
				<label class="custom-file-label">Adicionar mais arquivos</label>
			@else
				<label class="custom-file-label">Escolher arquivos</label>
			@endif
		</div>
	</div>

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

	<div class="form-group">
		<label class="font-weight-bold">Imagem do tamanho </label>
		<small class="text-secondary">(opcional)</small>
		<div class="custom-file">
			<input type="file" name="size_paths[]" accept="image/*" class="custom-file-input" multiple="multiple">
			@if ($method == 'PATCH')
				<label class="custom-file-label">Adicionar mais arquivos</label>
			@else
				<label class="custom-file-label">Escolher arquivos</label>
			@endif
		</div>
	</div>

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

	<div class="form-group">
		<label class="font-weight-bold">Comprovante de pagamento </label>
		<small class="text-secondary">(opcional)</small>
		<div class="custom-file">
			<input type="file" name="payment_voucher_paths[]" accept="image/*,.pdf" class="custom-file-input" multiple="multiple">
			@if ($method == 'PATCH')
				<label class="custom-file-label">Adicionar mais arquivos</label>
			@else
				<label class="custom-file-label">Escolher arquivos</label>
			@endif
		</div>
	</div>

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