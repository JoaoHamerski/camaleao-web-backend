<!DOCTYPE html>
<html lang="pt-BR">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Despesas</title>

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

		.expenses-types {
			margin-bottom: 2rem;
		}

		.expenses-types h4 {
			margin-bottom: .6rem;
		}

		.expenses-types div {
			margin-top: .3rem;
		}
	</style>
</head>
<body>
	<div class="title">
		Despesas de
		<strong>{{ Helper::date($start_date, '%d/%m/%Y') }}</strong>
		@if (! empty($end_date))
		~
		<strong>{{ Helper::date($end_date, '%d/%m/%Y') }}</strong>
		@endif
	</div>

	<div class="expenses-types">
		<h4>TOTAL DE GASTOS: </h4>
		@foreach($expensesByType as $key => $expenseValue)
			<div><strong>{{ $key }}</strong>: {{ Mask::money($expenseValue) }} </div>
		@endforeach
	</div>

	<table>
		<thead>
			<tr>
				<th>Descrição</th>
				<th>Tipo</th>
				<th>Via</th>
				<th>Valor</th>
				<th>Data</th>
			</tr>
		</thead>

		<tbody>
			@forelse($expenses as $expense)
				<tr>
					<td>{{ $expense->description }}</td>
					<td>{{ $expense->type->name }}</td>
					<td>{{ $expense->via->name }}</td>
					<td>{{ Mask::money($expense->value) }}</td>
					<td>{{ Helper::date($expense->date, '%d/%m/%Y') }}</td>
				</tr>
			@empty
				<tr>
					<td class="text-center" colspan="5">Nenhuma despesa encontrada</td>
				</tr>
			@endforelse
		</tbody>
	</table>
</body>
</html>
