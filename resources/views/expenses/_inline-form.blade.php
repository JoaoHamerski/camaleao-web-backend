<div class="form-inline-wrapper form-row d-flex flex-column flex-md-row">
	<div class="form-group col">
		<input class="form-control" type="text" placeholder="Descrição..." name="description[]">
	</div>

	<div class="form-group col">
		<select class="custom-select" name="type[]">
			<option value="">Selecione o tipo</option>
			@foreach($expenseTypes as $expenseType)
				<option value="{{ $expenseType->id }}">{{ $expenseType->name }}</option>
			@endforeach
		</select>
	</div>

	<div class="form-group col">
		<input class="form-control" type="text" name="value[]">
	</div>

	<div class="form-group col">
		<div class="input-group">
			<input class="form-control" type="text" name="date[]" placeholder="dd/mm/aaaa">
			<div class="input-group-append">
				<button class="btn btn-outline-primary btn-today">Hoje</button>
			</div>
		</div>
	</div>

	<div class="form-group col col-md-1">
		<button class="btn-delete btn-block btn btn-outline-danger">
			<i class="fas fa-trash-alt"></i>
		</button>
	</div>	
</div>

<hr class="d-block d-md-none">