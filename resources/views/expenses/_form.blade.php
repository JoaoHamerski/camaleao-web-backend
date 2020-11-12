<form>
  @if ($method == 'PATCH')
    @method('PATCH')
  @endif

  @input([
    'label' => 'Descrição',
    'labelClass' => 'font-weight-bold',
    'id' => 'description',
    'name' => 'description',
    'placeholder' => 'Descrição sobre a despesa...',
    'value' => $method == 'PATCH' ? $expense->description : ''
  ])

  @select([
    'label' => 'Tipo de despesa',
    'id' => 'expense_type_id',
    'name' => 'expense_type_id',
    'defaultOptionText' => 'Selecione um tipo de despesa',
    'items' => $expenseTypes,
    'itemAttribute' => 'name',
    'itemKeyToMatch' => $method == 'PATCH' ? $expense->expense_type_id : ''
  ])

  @if ($method == 'PATCH' && strcasecmp($expense->type->name, 'mão de obra') == 0)
    @input([
      'label' => 'Nome do funcionário',
      'labelClass' => 'font-weight-bold',
      'optional' => true,
      'id' => 'employee_name',
      'name' => 'employee_name',
      'value' => $expense->employee_name
    ])
  @endif  

  <div class="form-row d-flex flex-column flex-md-row">
    @input([
      'label' => 'Valor',
      'labelClass' => 'font-weight-bold',
      'formGroupClass' => 'col',
      'id' => 'value',
      'name' => 'value',
      'value' => $method == 'PATCH' ? Mask::money($expense->value) : ''
    ])

    @select([
      'label' => 'Via',
      'id' => 'expense_via_id',
      'name' => 'expense_via_id',
      'defaultOptionText' => 'Selecione a via',
      'formGroupClass' => 'col',
      'items' => $vias,
      'itemAttribute' => 'name',
      'itemKeyToMatch' => $method == 'PATCH' ? $expense->via->id : ''
    ])
  </div>

  @inputFile([
    'id' => 'receipt',
    'name' => 'receipt_path',
    'label' => 'Comprovante',
    'labelClass' => 'font-weight-bold', 
    'fileLabel' => 'Selecione o comprovante',
    'helpMessage' => $method == 'PATCH' && $expense->receipt_path
      ? 'Caso um comprovante seja selecionado, ele substituirá o atual'
      : ''
  ])

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

  @input([
    'label' => 'Data',
    'labelClass' => 'font-weight-bold',
    'name' => 'date',
    'id' => 'date',
    'placeholder' => 'dd/mm/aaaa',
    'value' => $method == 'PATCH' ? Helper::date($expense->date, '%d/%m/%Y') : '',
    'attributes' => [
      'autocomplete' => 'off',
      'data-toggle' => 'datepicker'
    ]
  ])

  <div class="d-flex justify-content-between">
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
  </div>
</form>