<span class="d-inline-block" @if (Auth::user()->hasRole('design') || $order->isClosed())
    v-tippy="{arrow: true, duration: 150, placement: 'bottom'}"
    @endif

    @if (Auth::user()->hasRole('design'))
    content="Você não tem permissão para isso"
    @elseif ($order->isClosed())
    content="Não é possível editar pedidos fechados"
    @endif
    >
    <a @class([ 'btn btn-outline-primary mx-2' , 'disabled'=> Auth::user()->hasRole('design') || $order->isClosed()
        ])
        @role(['atendimento', 'gerencia'])
        @if ($client)
        href="{{ route('orders.edit', [
        'client' => $client,
        'order' => $order
      ]) }}"
        @else
        href="{{ route('orders.editPreRegistered', ['order' => $order->id]) }}"
        @endif
        @endrole
        >
        <i class="fas fa-edit fa-fw mr-1"></i>Editar
    </a>
</span>
