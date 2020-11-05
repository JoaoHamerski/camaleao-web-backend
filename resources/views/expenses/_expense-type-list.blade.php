<li data-id="{{ $expenseType->id }}" class="list-group-item d-flex justify-content-between">
	<div class="expense-type-name">
		{{ $expenseType->name }}
	</div>

	<div class="d-flex">
		<div class="mr-2">
			<a href="" class="btn-edit-expense-type">Editar</a>
		</div>
{{-- 		<div>
			<a href="" class="text-danger btn-delete-expense-type">Deletar</a>
		</div> --}}
	</div>
</li>