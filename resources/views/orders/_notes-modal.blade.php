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
          @input([
            'name' => 'order_note',
            'placeholder' => 'Adicionar nota...',
            'inputGroup' => [
              'class' => 'mt-3',
              'btnAppend' => [
                'id' => 'btnAddNote',
                'class' => 'btn btn-outline-primary',
                'icon' => 'fas fa-plus',
                'text' => 'Adicionar'
              ]
            ]
          ])
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>
