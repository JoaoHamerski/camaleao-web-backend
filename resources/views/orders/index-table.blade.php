<table class="table table-hover">
	<thead>
		<tr>
			<th>Código</th>
			<th>Valor total</th>
			<th>Total pago</th>
			<th>Quantidade</th>
			<th>Data de produção</th>
			<th>Data de entrega</th>
		</tr>
	</thead>
	<tbody>
		@foreach ($orders as $order)
			<tr class="clickable-link @if ($order->is_closed) table-secondary @endif" 
				onclick="window.location = '{{ $order->path() }}'" 
				data-id="{{ $order->id }}">
				<td>{{ $order->code }}</td>
				<td>{{ Mask::money($order->price) }}</td>
				<td>{{ Mask::money($order->getTotalPayments()) }}</td>
				<td>{{ $order->quantity }}</td>
				<td>
					{{ 
					$order->production_date
						? Helper::date($order->production_date, '%d/%m/%Y')
						: '[não informado]' 
					}}
				</td>
				<td>
					{{ 
						$order->delivery_date 
						? Helper::date($order->delivery_date, '%d/%m/%Y')
						: '[não informado]' 
					}}
				</td>
			</tr>
		@endforeach
	</tbody>
</table>
