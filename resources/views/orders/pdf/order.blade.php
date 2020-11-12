<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html" charset="UTF-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>{{ $order->code }} - {{ $order->client->name }}</title>
	<style>
		* {
			font-family: sans-serif;
		}

		.page-break {
			page-break-after: always;
		}

		img {
			max-width: 100%;
			max-height: 50%;
		}

		.divider {
			margin-top: 1rem;
			border-bottom: 2px dashed black;
		}

		ul {
			list-style: none;
			font-size: 1.3rem;
		}

		ul li {
			margin-top: .25rem;
			margin-bottom: .25rem;
		}

		.text-danger {
			color: red;
		}

		.text-success {
			color: green;
		}

		.text-center {
			text-align: center;
		}
	</style>
</head>
<body>
	@if (isset($order->getPaths('art_paths')[0]))
		<img src="{{ Helper::imageTo64(public_path($order->getPaths('art_paths')[0])) }} ">
	@else
		[sem imagem cadastrada]
	@endif
	<ul>
		<li>
			<strong>Código:</strong> {{ $order->code }}
		</li>

		@if ($order->name)
		<li>
			<strong>Nome do pedido: </strong>{{ $order->name }}
		</li>
		@endif

		<li>
			<strong>Cidade:</strong> {{ $order->client->city ? $order->client->city : '[não-informado]' }}
		</li>

		<li>	
			<strong>Valor total: </strong> {!! Mask::money($order->price, true) !!}
		</li>

		<li>
			<strong>Quantidade:</strong> {{ $order->quantity }} {{ $order->quantity == 1 ? 'CAMISA' : 'CAMISAS' }}
		</li>

		<li>
			<strong>Total pago:</strong> {!! Mask::money($order->getTotalPayments(), true) !!}
		</li>

		<li class="{{ $order->isPaid() ? 'text-success' : 'text-danger' }}">
			<strong>Falta pagar:</strong> {!! Mask::money($order->getTotalOwing(), true) !!}
		</li>

		<li class="text-danger">
			<strong>Data de entrega:</strong> {{ $order->delivery_date ? Helper::date($order->delivery_date, '%d/%m/%Y') : '[não informado]' }}
		</li>

	</ul>

	<div class="page-break"></div>

	@if (isset($order->getPaths('art_paths')[1]))
		<img src="{{ Helper::imageTo64(public_path($order->getPaths('art_paths')[1])) }} ">	
	@else
		[sem imagem cadastrada]
	@endif
	
	<div>
		<ul>
			<li>
				<strong>Código: </strong> {{ $order->code }}
			</li>
			<li>
				<strong>Quantidade:</strong> {{ $order->quantity }} CAMISAS
			</li>
			<li class="text-danger">
				<strong>Data de entrega: </strong> {{ $order->delivery_date ? Helper::date($order->delivery_date, '%d/%m/%Y') : '[não informado]' }}
			</li>
			<li>
				<strong>Costureira / Valor: </strong>
			</li>
		</ul>
	</div>
</body>
</html>