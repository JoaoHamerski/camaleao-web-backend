<div class="modal fade" id="changesModal{{ $index }}" tabindex="-1" aria-labelledby="changesModal{{ $index }}Label" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title font-weight-bold" id="changesModal{{ $index }}Label">Relatório de alterações</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <ul class="list-group list-group-flush">
          @foreach ($changes['attributes'] as $field => $changed)
              @includeWhen(
                ! in_array($field, [
                  'client', 
                  'order', 
                  'payment', 
                  'updated_at', 
                  'confirmed_at'
                ]), 
                'activities._modal-apply-changes'
              )
          @endforeach
        </ul>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>