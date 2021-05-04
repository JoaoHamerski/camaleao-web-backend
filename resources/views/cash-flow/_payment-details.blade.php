<div>
	<h4 class="text-center text-success">Detalhes do pagamento</h4>

	<ul class="list-group list-group-flush">
		<li class="list-group-item">
			<strong>Valor: </strong>
			{{ Mask::money($payment->value) }}
		</li>

		<li class="list-group-item">
			<strong>Via: </strong>
			{{ $payment->via ? $payment->via->name : '[não informado]' }}
		</li>

		<li class="list-group-item">
			<strong>Data: </strong>
			{{ Helper::date($payment->date, '%d/%m/%Y') }}
		</li>

		

		@if ($payment->note)
		<li class="list-group-item">
			<strong>Anotação: </strong>
			{{ $payment->note }}
		</li>
		@endif
	</ul>

	<h4 class="text-center text-success mt-3">Detalhes do pedido pago</h4>

	<ul class="list-group list-group-flush">
		<li class="list-group-item">
			<strong>Código da camisa: </strong>
			{{ $payment->order->code }}
		</li>
		
		<li class="list-group-item">
			<strong>Nome da camisa: </strong>
			{{ $payment->order->name ?? '[não informado]' }}
		</li>
		
		<li class="list-group-item">
			<strong>Quantidade: </strong>
			{{ $payment->order->quantity }} CAMISAS
		</li>
		
		<li class="list-group-item">
			<strong>Cliente: </strong>
			{{ $payment->order->client->name }}
		</li>

		<li class="list-group-item">
			<strong class="d-block mb-2">Arte: </strong>
			@isset($payment->order->getPaths('art_paths')[0])
			<img class="img-fluid img-thumbnail" src="{{ $payment->order->getPaths('art_paths')[0] }}" alt="">
			@else
			<div class="text-center">[sem arte cadastrada]</div>
			@endisset
		</li>
	</ul>
</div>