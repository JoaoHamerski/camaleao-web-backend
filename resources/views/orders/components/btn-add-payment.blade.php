<span class="d-inline-block" @if ( Auth::user()->hasRole('design')
  || $order->isClosed()
  || $order->getTotalOwing() == 0
)
  v-tippy="{arrow: true, placement: 'bottom', duration: 150}"
@endif

@if (Auth::user()->hasRole('design'))
  content="Você não tem permissão para isso"
@elseif ($order->isClosed() || $order->getTotalOwing() == 0)
  content="Não é possível efetuar pagamentos pois o pedido está {{ $order->isClosed() ? 'fechado' : 'quitado' }}"
@endif
>
  <button class="btn btn-outline-success" 
    @role(['atendimento', 'gerencia' ]) 
      data-target="#newPaymentModal"
      data-toggle="modal" 
    @endrole 
    @if ( 
      Auth::user()->hasRole('design')
      || $order->isClosed()
      || $order->getTotalOwing() == 0
    )
      disabled="disabled"
    @endif
    >
    <i class="fas fa-dollar-sign fa-fw mr-1"></i> Adicionar pagamento
  </button>
</span>