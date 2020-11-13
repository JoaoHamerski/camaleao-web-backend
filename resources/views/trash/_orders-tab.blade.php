<div class="table-responsive">
	<table class="table">
		<thead>
			<tr>
				<th>Cliente</th>
				<th>Código</th>
				<th>Valor total</th>
				<th>Deletado em</th>
				<th class="text-center">Recuperar</th>
				<th class="text-center">Deletar</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($orders as $order)
				<tr>
					<td>{{ $order->client->name }}</td>
					<td>{{ $order->code }}</td>
					<td>{{ Mask::money($order->price) }}</td>
					<td>{{ Helper::date($order->deleted_at, '%d/%m/%Y') }}</td>
					<td class="text-center">
						<button class="btn btn-outline-success">
							<i class="fas fa-trash-restore-alt"></i>
						</button>
					</td>
					<td class="text-center">
						<button class="btn btn-outline-danger">
							<i class="fas fa-trash-alt"></i>
						</button>
					</td>
				</tr>
			@endforeach
		</tbody>
	</table>
</div>	