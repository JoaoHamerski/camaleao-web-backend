<div class="modal fade" id="clientEditModal" tabindex="-1" aria-labelledby="clientEditModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title font-weight-bold" id="clientEditModalLabel">
          <i class="fas fa-user-plus fa-fw mr-2"></i>Atualizar dados de cliente
        </h5>

        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        @include('clients._form', ['method' => 'PATCH'])
      </div>
    </div>
  </div>
</div>