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

  <div class="d-flex justify-content-between">
    <button class="btn btn-success" type="submit">Alterar</button>
    <button data-dismiss="modal" class="btn btn-light">Fechar</button>
  </div>
</form>