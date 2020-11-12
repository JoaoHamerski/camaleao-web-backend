<div data-index="{{ $index ?? 0 }}" class="form-inline-wrapper d-flex flex-column no-gutters">
	<div class="form-row d-flex flex-column flex-lg-row col">
		<div class="form-group col">
			<input class="form-control" type="text" placeholder="Descrição..." name="description[{{ $index ?? 0 }}]">
		</div>

		<div class="form-group col">
			<input class="form-control" type="text" name="value[{{ $index ?? 0 }}]">
		</div>

		<div class="form-group col">
			<div class="input-group">
				<input class="form-control" type="text" name="date[{{ $index ?? 0 }}]" placeholder="dd/mm/aaaa">
				<div class="input-group-append">
					<button class="btn btn-outline-primary btn-today">Hoje</button>
				</div>
			</div>
		</div>
	</div>

	<div class="form-row d-flex flex-column flex-lg-row col">
		<div class="form-group col">
			<select class="custom-select" name="expense_type_id[{{ $index ?? 0 }}]">
				<option value="">Selecione o tipo</option>
				@foreach($expenseTypes as $type)
					<option value="{{ $type->id }}">{{ $type->name }}</option>
				@endforeach
			</select>
		</div>


		<div class="form-group col">
			<select class="custom-select" name="expense_via_id[{{ $index ?? 0 }}]">
				<option value="">Selecione a via</option>
				@foreach($vias as $via)
					<option value="{{ $via->id }}">{{ $via->name }}</option>
				@endforeach
			</select>
		</div>

		<div class="form-group col">
			<div class="custom-file">
				<input class="custom-file-input" name="receipt_path[{{ $index ?? 0 }}]" type="file" accept="image/*,.pdf">
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