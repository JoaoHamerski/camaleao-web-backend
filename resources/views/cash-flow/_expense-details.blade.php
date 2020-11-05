<div>
	<h4 class="text-center text-danger">Detalhes da despesa</h4>

	<ul class="list-group list-group-flush">
		<li class="list-group-item">
			<strong>Descrição: </strong>
			{{ $expense->description ?? '[sem descrição]' }}
		</li>

		<li class="list-group-item">
			<strong>Valor: </strong>
			{{ Mask::money($expense->value) }}
		</li>

		<li class="list-group-item">
			<strong>Tipo: </strong>
			{{ $expense->type->name }}
		</li>

		<li class="list-group-item">
			<strong>Via: </strong>
			{{ $expense->via->name }}
		</li>

		@if ($expense->type->id == 9)
		<li class="list-group-item">
			<strong>Com funcionário:</strong> 
			{{ $expense->employee_name ?? '[não informado]' }}
		</li>
		@endif
		
		<li class="list-group-item">
			<strong>Cadastrado por: </strong>
			{{ $expense->user->name ?? '[usuário deletado]' }}
		</li>


		<li class="list-group-item">
			<strong>Data: </strong>
			{{ Helper::date($expense->date, '%d/%m/%Y') }}
		</li>

		<li class="list-group-item">
			<strong class="d-block mb-2">Comprovante: </strong>
			@if($expense->receipt_path)
				<img class="img-fluid img-thumbnail" src="{{ $expense->getReceiptPath() }}" >
			@else
				<div class="text-center">[sem comprovante cadastrado]</div>
			@endif
		</li>
	</ul>
</div>