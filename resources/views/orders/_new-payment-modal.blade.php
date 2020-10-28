<div class="modal fade" id="newPaymentModal" tabindex="-1" aria-labelledby="newPaymentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title font-weight-bold" id="newPaymentModalLabel">
          <i class="fas fa-dollar-sign fa-fw mr-1"></i>Novo pagamento
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="" method="POST">
          <div class="form-group">
            <label class="font-weight-bold" for="value">Valor </label>
            <input class="form-control" id="value" name="value" type="text">  
          </div>

          <div class="form-group">
            <label class="font-weight-bold" for="date">Data do pagamento</label>
            <div class="input-group">
              <input placeholder="dd/mm/aaaa" class="form-control" id="date" name="date" type="text">
              <div class="input-group-append">
                <button class="btn btn-outline-primary btn-today">Hoje</button>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label class="font-weight-bold" for="note">Observação</label> <small class="text-secondary">(opcional)</small>
            <input placeholder="Anotação extra sobre o pagamento..." type="text" class="form-control" id="note" name="note">
          </div>

          <div class="mt-3">
            <button id="btnAddPayment" data-id="{{ $order->id }}" type="submit" class="btn btn-primary">Salvar</button>
            <button type="button" class="btn btn-light" data-dismiss="modal">Fechar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>