<form>
  <div class="form-group">
    <label class="font-weight-bold" for="description">Descrição</label>
    <input class="form-control" 
      type="text" 
      name="description" 
      @if ($method == 'PATCH') value="{{ $expense->description }}" @endif >
  </div>

  <div class="form-group">
    <label class="font-weight-bold" for="expense_type_id">Tipo de despesa</label>
    <select class="custom-select" name="expense_type_id">
      <option value="">Seleciona um tipo de despesa</option>

      @if ($method == 'PATCH')
        @foreach($expenseTypes as $expenseType)
          <option @if($expenseType->id == $expense->expense_type_id) selected="selected" @endif value="{{ $expenseType->id }}">{{ $expenseType->name }}</option>
        @endforeach
      @else
          @foreach($expenseTypes as $expenseType)
            <option value="{{ $expenseType->id }}">{{ $expenseType->name }}</option>
          @endforeach
      @endif
    </select>
  </div>

  <div class="form-group">
    <label class="font-weight-bold" for="value">Valor</label>
  	<input class="form-control" 
      type="text" 
      name="value" 
      @if($method == 'PATCH') value="{{ Mask::money($expense->value) }}" @endif>
  </div>

  <div class="form-group">
    <label for="date" class="font-weight-bold">Data</label>
  	<input class="form-control" type="text" id="date" name="date" placeholder="dd/mm/aaaa" 
      @if($method == 'PATCH') value="{{ Helper::date($expense->date, '%d/%m/%Y') }}" @endif>
  </div>

  @if ($method == 'PATCH')
    <button data-id="{{ $expense->id }}" id="btnUpdateExpense" class="btn btn-success" type="submit">
      <i class="fas fa-check fa-fw mr-1"></i>Atualizar
    </button>
  @else
    <button id="btnCreateUniqueExpense" class="btn btn-success">
      <i class="fas fa-check fa-fw mr-1"></i>Cadastrar
    </button>
  @endif

  <button class="btn btn-light" data-dismiss="modal">Cancelar</button>
</form>