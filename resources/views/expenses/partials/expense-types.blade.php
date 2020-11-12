<ul class="list-group">
  @foreach($expenseTypes as $expenseType)
    @include('expenses.partials.expense-type-item', ['expenseType' => $expenseType])
  @endforeach
</ul>

<form>
  <div class="form-group mt-3">
    <div class="input-group">
      <input class="form-control" name="expense_type" type="text" placeholder="Tipo de despesa...">
      <div class="input-group-append">
        <button id="btnAddExpenseType" class="btn btn-outline-primary">
        <i class="fas fa-plus fa-fw mr-1"></i> Adicionar
        </button>
      </div>
    </div>
  </div>
</form>