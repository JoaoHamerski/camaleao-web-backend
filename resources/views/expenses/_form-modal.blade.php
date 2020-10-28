<form>
  @if ($method == 'PATCH')
    @method('PATCH')
  @endif

  <div class="form-group">
    <label class="font-weight-bold" for="description">Descrição</label>
    <input class="form-control" 
      type="text" 
      name="description" 
      @if ($method == 'PATCH') value="{{ $expense->description }}" @endif >
  </div>

  <div class="form-group">
    <label class="font-weight-bold" for="expense_type_id">Tipo de despesa</label>
    <select class="custom-select" id="expense_type_id" name="expense_type_id">
      <option value="">Seleciona um tipo de despesa</option>
        @foreach($expenseTypes as $expenseType)
          <option @if($method == 'PATCH'  && $expenseType->id == $expense->expense_type_id) selected="selected" @endif value="{{ $expenseType->id }}">{{ $expenseType->name }}</option>
        @endforeach
    </select>
  </div>


  <div class="form-row d-flex flex-column flex-md-row">
    <div class="form-group col">
      <label class="font-weight-bold" for="value">Valor</label>
    	<input class="form-control" 
        type="text" 
        name="value" 
        @if($method == 'PATCH') value="{{ Mask::money($expense->value) }}" @endif>
    </div>
    <div class="form-group col">
      <label for="expense_via_id" class="font-weight-bold">Via</label>
      <select class="custom-select" name="expense_via_id" id="expense_via_id">
        <option value="">Selecione a via</option>

        @foreach($expenseVias as $via)
          <option @if ($method == 'PATCH' && $via->id == $expense->expenseVia->id) selected="selected" @endif value="{{ $via->id }}">{{ $via->name }}</option>
        @endforeach
      </select>
    </div>
  </div>

    <div class="form-group">
      <label class="font-weight-bold" for="recipt">Comprovante</label> <small class="text-muted">(opcional)</small>
      <div class="custom-file">
        <input class="custom-file-input" name="receipt_path" type="file" accept="image/*,.pdf">
        <label class="custom-file-label">Selecione o comprovante</label>
        @if ($method == 'PATCH' && $expense->receipt_path)
        <small class="text-muted">Caso um comprovante seja selecionado, ele substituirá o atual.</small>
        @endif
      </div>

      @if ($method == 'PATCH' && $expense->getReceiptPath())
        <div data-id="{{ $expense->id }}">
          @if (Helper::isImage($expense->getReceiptPath()))
            <div class="col-md-5 px-0 mt-2">  
              <div class="position-relative img-wrapper">
                <a class="stretched-link" target="_blank" href="{{ $expense->getReceiptPath() }}"></a>
                <img class="img-thumbnail img-fluid" src="{{ $expense->getReceiptPath() }}" alt="">
                <div id="deleteReceipt" class="btn-delete-image">X</div>
              </div>
            </div>
          @else
            <ul class="list-group mt-2">
              <li class="list-group-item d-flex justify-content-between">
                <a target="_blank" href="{{ $expense->getReceiptPath() }}">Comprovante</a>
                <a id="deleteReceipt" href="" class="text-danger">Deletar</a>
              </li>
            </ul>
          @endif
        </div>
      @endif
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