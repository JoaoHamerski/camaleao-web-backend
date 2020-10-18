<div class="modal fade" id="notesModal" tabindex="-1" aria-labelledby="notesModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary">
        <h5 class="modal-title font-weight-bold text-white" id="notesModalLabel">
          <i class="fas fa-sticky-note fa-fw"></i>Anotações
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <ul id="listGroupNotes" class="list-group">
          @foreach($order->notes as $note)
            @include('orders._note-list-item')
          @endforeach
        </ul>

        <form>
          <div class="input-group mt-3">
            <input class="form-control" type="text" placeholder="Adicionar nota..." name="order_note">
            <div class="input-group-append">
              <button id="btnAddNote" class="btn btn-outline-primary">
                <i class="fas fa-plus fa-fw mr-1"></i>Adicionar
              </button>
            </div>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>
