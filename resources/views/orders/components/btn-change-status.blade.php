<span class="d-inline-block"
  @if ($order->isClosed())
    v-tippy="{arrow: true, duration: 150, placement: 'bottom'}"
    content="Não é possível alterar os status de pedidos está fechados"
  @endif
>
  <button class="btn btn-outline-primary"
    @if (! $order->isClosed())
      data-target="#statusModal"
      data-toggle="modal"
    @else
      disabled="disabled"
    @endif
  >
    Alterar status
  </button>
</span>