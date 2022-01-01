<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html" charset="UTF-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Relatório produção do dia {{ $data['date'] }}</title>
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
			/*width: 100%;*/
			max-height: 400px;
		}

		@page { margin: 20px 30px 40px 50px; }

		footer .page:after {
        	content: counter(page, decimal);
        }

        footer {
			position: fixed;
			right: 0px;
			bottom: 10px;
			text-align: center;
			border-top: 1px solid black;
		}
	</style>
</head>
<body>
	<div class="title">
		Relatório de pedidos por produção do dia
		<br>
		<br>
		<strong>{{ $data['date'] }}  |  TOTAL {{ $data['total_quantity'] }} PEÇAS</strong>
	</div>

	<footer>
    	<p class="page">Página </p>
  	</footer>

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
