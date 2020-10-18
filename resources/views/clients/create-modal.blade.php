<div class="modal fade" id="clientCreateModal" tabindex="-1" aria-labelledby="clientCreateModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title font-weight-bold" id="clientCreateModalLabel">
          <i class="fas fa-user-plus fa-fw mr-2"></i>Cadastrar novo cliente
        </h5>

        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        @include('clients._form')
      </div>
    </div>
  </div>
</div>
