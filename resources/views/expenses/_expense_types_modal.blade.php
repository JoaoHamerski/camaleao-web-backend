<div class="modal fade" id="expenseTypesModal" tabindex="-1" aria-labelledby="expenseTypesModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary">
        <h5 class="modal-title font-weight-bold text-white" id="expenseTypesModalLabel">
          <i class="fas fa-list fa-fw mr-1"></i>Tipos de despesas
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <ul class="list-group">
          @foreach($expenseTypes as $expenseType)
            @include('expenses._expense-type-list', ['expenseType' => $expenseType])
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
      </div>
    </div>
  </div>
</div>