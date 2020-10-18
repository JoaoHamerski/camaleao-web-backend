<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html" charset="UTF-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Relatório produção do dia {{ Helper::date($date, '%d/%m/%Y') }}</title>
	<style>
		* {
			font-family: sans-serif;
		}

		.title {
			text-align: center;
			font-size: 1.3rem;
			margin-bottom: 2rem;
		}

		.text-center {
			text-align: center;
		}

		.page-break {
			page-break-after: always;
		}

		img	{
			width: 100%;
		}
	</style>
</head>
<body>
	<div class="title">
		Relatório de pedidos por produção do dia
		<br>
		<br>
		<strong>{{ Helper::date($date, '%d/%m/%Y') }}  |  TOTAL {{ $totalQuantity }} PEÇAS</strong>
	</div>

	@foreach($orders as $order)
		<div class="text-center" style="font-size: 1.2rem">
			<strong>CÓDIGO:</strong> {{ $order->code }} | <strong>CLIENTE:</strong> {{ $order->client->name }}
		</div>
		<br>

		<div class="text-center">
			@isset($order->getPaths('size_paths')[0])
				<img src="{{ Helper::imageTo64(public_path($order->getPaths('size_paths')[0])) }}">
			@else
				[sem imagem]
			@endisset
		</div>
		<br>

		<div class="text-center">
			@isset($order->getPaths('size_paths')[1])
				<img src="{{ Helper::imageTo64(public_path($order->getPaths('size_paths')[1])) }}">
			@else
				[sem imagem]
			@endisset
		</div>

		@if (! $loop->last)
			<div class="page-break"></div>
		@endif
	@endforeach

</body>
</html>