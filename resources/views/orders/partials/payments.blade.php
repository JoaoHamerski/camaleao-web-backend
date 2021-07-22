<h5 class="font-weight-bold text-secondary mt-4 mb-3">
  &bull; Pagamentos
</h5>

<div class="d-flex flex-column">
  <ul class="list-group list-group-flush">
    @forelse($payments as $payment)
      <li data-id="{{ $payment->id }}" 
        class="list-group-item d-flex justify-content-between 
        @if($payment->is_confirmed === null) list-group-item-warning @elseif($payment->is_confirmed == false) list-group-item-danger @endif"
      >
        <div>
          <strong>
            {{ Mask::money($payment->value) }}
          </strong> 
          em 
          <strong>
            {{ Helper::date($payment->date, '%d/%m/%Y') }}
          </strong>

          @if ($payment->via)
            via <strong>{{ $payment->via->name }}</strong>
          @endif

          @if (! empty($payment->note))
            -
            <a onclick="event.preventDefault()" 
              href="" 
              data-toggle="tooltip" 
              title="{{ $payment->note }}">
              (ver anotação)
            </a>
          @endif

          @if ($payment->is_confirmed === null)
            <span class="font-weight-bold"> - [PENDENTE]</span>
          @elseif($payment->is_confirmed == false)
            <span class="font-weight-bold"> - [RECUSADO]</span>
          @endif
        </div>

        <div>
          @role(['gerencia', 'atendimento'])
            @if ($payment->is_confirmed === null)
              <button class="btn btn-sm btn-outline-primary" 
                data-toggle="modal" 
                  data-target="#changePaymentModal">
                <i class="fas fa-edit fa-fw"></i>
              </button>
            @endif
          @else
            @if ($payment->is_confirmed === null)
              @button([
                'title' => 'Você não tem permissão para isso',
                'icon' => 'fas fa-edit',
                'class' => 'btn btn-outline-primary btn-sm'
              ])
            @endif
          @endrole
        </div>
      </li>
    @empty
      <li class="list-group-item text-center">
        <h5 class="text-secondary">Nenhum pagamento feito ainda</h5>
      </li>
    @endforelse
  </ul>
</div>