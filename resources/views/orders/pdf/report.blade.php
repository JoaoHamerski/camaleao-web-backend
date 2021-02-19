<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html" charset="UTF-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>
		Relatório de pedidos 
		@if ($request->filled('cidade')) - {{ $request->cidade }} @endif 
		@if ($request->filled('status')) - {{ \App\Models\Status::find($request->status)->text }} @endif
		@if ($request->filled('data_de_fechamento'))
		 - fechados em {{ Helper::date($request->data_de_fechamento, '%d/%m/%Y') }} 
		 @endif
	</title>
	<style>
		* {
			font-family: sans-serif;
		}

		table {
			width: 100%;
			border-collapse: collapse;
			padding-bottom: .25rem;
			page-break-inside: avoid !important;
		}

		table tr, table td, table th {
			padding-left: .4rem;
			padding-right: .4rem;
			padding-top: .25rem;
			padding-bottom: .25rem;
			border: 1px solid black;
		}

		table tr {
			word-wrap: break-word;
		}

		.text-center {
			text-align: center;
		}

		header {
			text-align: center;
			font-size: 1.3rem;
			margin-bottom: 2rem;
		}

		.text-muted {
			color: rgba(0, 0, 0, .3);
		}

		.image, .image img {
			width: 250px !important;
		}

		footer { 
			position: fixed; 
			right: 0px; 
			bottom: 10px; 
			text-align: center;
			border-top: 1px solid black;
		}

        footer .page:after { 
        	content: counter(page, decimal); 
        }

        .page-break {
        	page-break-before: always;
        }

        .note {
        	font-size: .9rem;
        }

 		@page { margin: 20px 30px 40px 50px; }
	</style>
</head>
<body>
	<footer>
    	<p class="page">Página </p>
  	</footer> 

	<header>
		Relatório de pedidos 
		@if ($request->filled('cidade'))
			<br>
			<br>
			<strong style="text-transform: uppercase;">{{ $request->cidade }}</strong>
		@endif
		
		@if ($request->filled('status'))
			<br>
			<div style="font-size: 1.2rem; margin-top: .25rem;">
				{{ \App\Models\Status::find($request->status)->text }}
			</div>
		@endif
		@if ($request->filled('data_de_fechamento'))
			<br>
			<div style="font-size: 1rem">
				<strong>Fechados em {{ $request->data_de_fechamento }}</strong>
			</div>
		@endif
	</header>

	@foreach($orders as $order)
		<table>	
			<tbody>
				<tr>
					<td class="image text-center" 
						rowspan="{{ $order->notes->count() 
								? $order->notes->count() + 4
								: 3 }}">
						@isset($order->getPaths('art_paths')[0])
							<div>
								<img width="100px" 
								src="{{ Helper::imageTo64(public_path($order->getPaths('art_paths')[0])) }}">
							</div>
						@else
							[sem imagem]
						@endisset
					</td>
					<td><strong>Cliente: </strong> {{ $order->client->name }}</td>
				</tr>

				<tr><td><strong>Código: </strong>{{ $order->code }}</td></tr>

				<tr>
					<td>
						<strong>Quantidade: </strong>
						{{ $order->quantity }} 
						{{ $order->quantity == 1 ? 'CAMISA' : 'CAMISAS' }} 
					</td>
				</tr>

				@if ($order->notes->count())
					<tr><td class="text-center"><strong>Anotações</strong></td></tr>

					@foreach($order->notes as $note)
						<tr><td class="note">{{ $note->text }}</td></tr>
					@endforeach
				@endif
			</tbody>
		</table>
	@endforeach
</body>
</html>