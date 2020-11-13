<div class="table-responsive">
	<table class="table">
		<thead>
			<tr>
				<th>Nome</th>
				<th>Telefone</th>
				<th>Cidade</th>
				<th>Deletado em</th>
				<th class="text-center">Recuperar</th>
				<th class="text-center">Deletar</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($clients as $client)
				<tr>
					<td>{{ $client->name }}</td>
					<td>{{ $client->phone ? Mask::phone($client->phone) : '[não informado]' }}</td>
					<td nowrap="nowrap">{{ $client->city ?? '[não informado]' }}</td>
					<td>{{ Helper::date($client->deleted_at, '%d/%m/%Y') }}</td>
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