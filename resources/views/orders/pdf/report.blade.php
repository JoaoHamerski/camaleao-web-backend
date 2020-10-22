<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html" charset="UTF-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>
		Relatório de pedidos @if ($city != null) - {{ $city }} @endif @if ($status != null) - {{ $status->text }} @endif
	</title>
	<style>
		* {
			font-family: sans-serif;
		}

		table {
			width: 100%;
			border-collapse: collapse;
		}

		table tr, table td, table th {
			padding: .25rem;
			border: 1px solid black;
		}

		.text-center {
			text-align: center;
		}

		.title {
			text-align: center;
			font-size: 1.3rem;
			margin-bottom: 2rem;
		}

		.text-muted {
			color: rgba(0, 0, 0, .3);
		}
	</style>
</head>
<body>
	<div class="title">
		Relatório de pedidos 
		@if ($city != null)
			<br>
			<br>
			<strong style="text-transform: uppercase;">{{ $city }}</strong>
		@endif
		
		@if ($status != null)
			<br>
			<div style="font-size: 1.2rem; margin-top: .25rem;">{{ $status->text }}</div>
		@endif
	</div>
	<table class="table">
		<thead>
			<tr>
				<th>Cliente</th>
				<th>Código do pedido</th>
				<th>Observações</th>
			</tr>
		</thead>

		<tbody>
			@foreach($orders as $order)
				<tr>
					<td class="text-center" rowspan="{{ count($order->notes) }}">
						{{ $order->client->name }}
					</td>	
					<td class="text-center" rowspan="{{ count($order->notes) }}">
						{{ $order->code }}
					</td>
					@isset($order->notes[0]->text)
						<td>{{ $order->notes[0]->text }}</td>
					@else
						<td class="text-center text-muted">[sem observações]</td>
					@endisset
				</tr>
					@foreach($order->notes as $note)
						@if ($loop->index != 0)
						<tr>
							<td>{{ $note->text }}</td>
						</tr>
						@endif
					@endforeach
			@endforeach
		</tbody>
	</table>
</body>
</html>