@role(['atendimento', 'gerencia'])
  <form id="toggleOrderForm" 
    method="POST" 
    class="d-none" 
    action="{{ 
      route('orders.toggleOrder', [
        'client' => $client, 
        'order' => $order
      ]) 
    }}"
  >
    @csrf
  </form>
@endrole

<span class="d-inline-block" 
  @if (
    Auth::user()->hasRole('design') 
    || $order->getTotalOwing() > 0
    || $order->isPreRegistered()
  )
    v-tippy="{arrow: true, duration: 150, placement: 'bottom'}"
  @endif

  @if (Auth::user()->hasRole('design'))
    content="Você não tem permissão para isso"
  @elseif ($order->getTotalOwing() > 0)
    content="Não é possivel fechar pedidos com pendência financeira"
  @elseif ($order->isPreRegistered())
    content="Não é possível fechar pedidos em pré-registro"
  @endif
  >
  <button class="btn btn-outline-secondary d-block" 
    @role(['gerencia', 'atendimento' ]) 
      @click.prevent="() => {
        this.document.querySelector('#toggleOrderForm').submit()
      }" 
    @endrole 

    @if (
      Auth::user()->hasRole('design') 
      || $order->getTotalOwing() > 0
      || $order->isPreRegistered()
    )
      disabled="disabled"
    @endif
  >
    {{ $order->isClosed() ? 'Reabrir pedido' : 'Fechar pedido' }}
  </button>
</span>