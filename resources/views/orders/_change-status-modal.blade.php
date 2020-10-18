<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-primary">
        <h5 class="modal-title font-weight-bold text-white" id="statusModalLabel">Alterar status</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="{{ route('status.patch', ['client' => $client, 'order' => $order]) }}" method="POST">
          @csrf
          <div class="form-group">
            <select class="custom-select" name="status" id="status">
              <option value="">Selecione o status</option>
              @foreach($status as $stat)
                <option @if($stat->id == $order->status->id) selected="selected" @endif value="{{ $stat->id }}">{{ $stat->text }}</option>
              @endforeach
            </select>
          </div>
          <button class="btn btn-success" type="submit">Alterar</button>
        </form>
      </div>
    </div>
  </div>
</div>