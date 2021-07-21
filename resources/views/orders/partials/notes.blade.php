<ul id="listGroupNotes" class="list-group">
  @foreach($order->notes()->whereNull('is_reminder')->get() as $note)
    @include('orders.partials.note-list-item')
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