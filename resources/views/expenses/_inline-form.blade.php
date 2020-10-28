<div class="form-inline-wrapper d-flex flex-column no-gutters">
	<div class="form-row d-flex flex-column flex-lg-row col">
		<div class="form-group col">
			<input class="form-control" type="text" placeholder="Descrição..." name="description[]">
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
	</div>

	<div class="form-row d-flex flex-column flex-lg-row  col">
		<div class="form-group col">
			<select class="custom-select" name="expense_type_id[]">
				<option value="">Selecione o tipo</option>
				@foreach($expenseTypes as $expenseType)
					<option value="{{ $expenseType->id }}">{{ $expenseType->name }}</option>
				@endforeach
			</select>
		</div>


		<div class="form-group col">
			<select class="custom-select" name="expense_via_id[]">
				<option value="">Selecione a via</option>
				@foreach($expenseVias as $expenseVia)
					<option value="{{ $expenseVia->id }}">{{ $expenseVia->name }}</option>
				@endforeach
			</select>
		</div>

		<div class="form-group col">
			<div class="custom-file">
				<input class="custom-file-input" name="receipt_path[]" type="file" accept="image/*,.pdf">
				<label class="custom-file-label" for="receipt_path">Comprovante</label>
			</div>
		</div>
	
	</div>
	
	<div class="form-group col col-md-1">
		<button class="btn-delete btn-block btn btn-outline-danger">
			<i class="fas fa-trash-alt"></i>
		</button>
	</div>	
</div>


<hr class="d-block d-lg-none">