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
        @include('orders._payment-form', [
          'method' => 'POST'
        ])
      </div>
    </div>
  </div>
</div>