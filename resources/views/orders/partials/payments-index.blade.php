<h4 class="font-weight-bold text-secondary mt-4 mb-3">Pagamentos</h4>

<div class="d-flex flex-column">
  <ul class="list-group list-group-flush">
    @forelse($order->payments->reverse() as $payment)
      <li data-id="{{ $payment->id }}" class="list-group-item d-flex justify-content-between">
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
        </div>

        <div>
          @role(['gerencia', 'atendimento'])
            <button class="btn btn-sm btn-outline-primary" 
              data-toggle="modal" 
                data-target="#changePaymentModal">
              <i class="fas fa-edit fa-fw"></i>
            </button>
          @else
            @button([
              'title' => 'Você não tem permissão para isso',
              'icon' => 'fas fa-edit',
              'class' => 'btn btn-outline-primary btn-sm'
            ])
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