@extends('layout')

@section('title', 'Pedido - ' . $order->code)

@section('content')
  <div class="row mt-5">
    <div class="col-md-3">
      <a class="btn btn-outline-primary mb-2" href="{{ $client->path() }}">
        <i class="fas fa-arrow-circle-left fa-fw mr-1"></i>Voltar
      </a>
      @include('clients._client-card')
    </div>

    <div class="col-md-9 mt-3 mt-md-0">
      <div class="d-flex flex-column flex-md-row justify-content-between mb-2">
        <div class="mt-3 mt-md-0">
          @role(['atendimento', 'gerencia'])
            @if ($order->isClosed() || $order->getTotalOwing() == 0)
              @button([
                'title' => 'Não é possível efetuar pagamentos pois o pedido está ' 
                  . (! $order->isClosed() ? 'quitado' : 'fechado'),
                'class' => 'btn btn-outline-success',
                'wrapperClass' => 'w-100',
                'icon' => 'fas fa-dollar-sign',
                'text' => 'Adicionar pagamento'
              ])
            @else
              <a class="btn btn-outline-success d-block" href="#newPaymentModal" data-toggle="modal">
                <i class="fas fa-dollar-sign fa-fw mr-1"></i>Adicionar pagamento
              </a>
            @endif
          @else('design')
            @button([
                'title' => 'Você não tem permissão para isso ', 
                'class' => 'btn btn-outline-success',
                'wrapperClass' => 'w-100',
                'icon' => 'fas fa-dollar-sign',
                'text' => 'Adicionar pagamento'
              ])
          @endrole
        </div>

        <div class="mt-3 mt-md-0">
          @role(['atendimento', 'gerencia'])
            <form id="toggleOrderForm" method="POST" class="d-none" 
              action="{{ 
                route('orders.toggleOrder', [
                  'client' => $client, 
                  'order' => $order
                ]) 
              }}">
                @csrf
            </form>

            @if ($order->getTotalOwing() == 0 || $order->isClosed())
              <a class="btn btn-outline-secondary d-block" onclick="event.preventDefault; document.querySelector('#toggleOrderForm').submit()">{{ $order->isClosed() ? 'Reabrir pedido' : 'Fechar pedido' }}</a>
            @else
              @button([
                'title' => 'Não é possível fechar pedidos com pendência financeira', 
                'class' => 'btn btn-outline-secondary',
                'wrapperClass' => 'w-100',
                'text' => $order->isClosed() ? 'Reabrir pedido' : 'Fechar pedido'
              ])
            @endif


          @else('design')
            @button([
                'title' => 'Você não tem permissão para isso', 
                'class' => 'btn btn-outline-secondary',
                'wrapperClass' => 'w-100',
                'text' => $order->isClosed() ? 'Reabrir pedido' : 'Fechar pedido'
              ])
          @endrole
        </div>

        <div class="d-flex justify-content-between mt-3 mt-md-0">
          <a target="_blank" class="btn btn-primary" href="{{ route('orders.order-pdf', ['client' => $client, 'order' => $order]) }}">
            <i class="fas fa-file-invoice fa-fw mr-1"></i>Gerar relatório
          </a>

          @role(['atendimento', 'gerencia'])
            @if ($order->isClosed())
              @button([
                'title' => 'Não é possível editar pois o pedido está fechado', 
                'class' => 'btn btn-outline-primary mx-2',
                'icon' => 'fas fa-edit',
                'text' => 'Editar'
              ])
            @else
              <a class="btn btn-outline-primary mx-2" 
                href="{{ route('orders.edit', ['client' => $client, 'order' => $order]) }}">
                <i class="fas fa-edit fa-fw mr-1"></i>Editar
              </a>
            @endif
          @else
            @button([
              'title' => 'Você não tem permissão para isso',
              'class' => 'btn btn-outline-primary mx-2',
              'icon' => 'fas fa-edit',
              'text' => 'Editar'
            ])
          @endrole

          @role(['atendimento', 'gerencia'])
            <a class="btn btn-outline-danger" id="btnDeleteOrder" href="">
              <i class="fas fa-trash-alt fa-fw mr-1"></i>Excluir
            </a>
          @else
            @button([
              'title' => 'Voce não tem permissão para isso',
              'class' => 'btn btn-outline-danger',
              'icon' => 'fas fa-trash-alt',
              'text' => 'Excluir'
            ])
          @endrole
        </div>
      </div>
      
      <div class="card">
        <div class="card-header {{ $order->isClosed() ?  'bg-secondary' : 'bg-primary' }} font-weight-bold text-white">
          <i class="fas fa-box-open fa-fw mr-1"></i>
          Pedido - {{ $order->name ?? $order->code }} 
          @if ($order->isClosed())
            - FECHADO EM {{ Helper::date($order->closed_at, '%d/%m/%Y') }} 
          @endif
        </div>

        <div class="card-body">
          <div class="mb-3 d-flex flex-row justify-content-between">
            <button data-toggle="modal" data-target="#notesModal" class="btn btn-outline-primary">
              <i class="fas fa-sticky-note fa-fw mr-1"></i>Anotações ({{ $order->notes->count() }})
            </button>

            @if ($order->isClosed())
              @button([
                'title' => 'Não é possível alterar os status pois o pedido está fechado',
                'text' => 'Alterar status',
                'class' => 'btn btn-outline-primary'
              ])
            @else
              <button data-target="#statusModal" data-toggle="modal" class="btn btn-outline-primary">Alterar status</button>
            @endif
          </div>

          <div class="mb-4">
            <h4 class="font-weight-bold text-secondary">Detalhes do pedido ({{ $order->code }})</h4>
          </div>

          <div class="mb-3">
            <h5 class="font-weight-bold text-dark">
              {{ $order->name }}
            </h5>
          </div>

          <div class="d-block d-md-none mb-2">
            <div class="@if($order->status->id == 8) text-success @else text-warning @endif">
              <h5>Status</h5>
              <div class="font-weight-bold ">{{ $order->status->text }}</div>
            </div>
          </div>

          <div class="d-flex justify-content-between">
            <div class="d-none d-md-block @if($order->status->id == 8) text-success @else text-warning @endif">
              <h5>Status</h5>
              <div class="font-weight-bold ">{{ $order->status->text }}</div>
            </div>

            <div>
              <h5>Valor total</h5>
              <div>{!! Mask::money($order->price, true) !!}</div>
            </div>

            <div>
              <h5>Total pago</h5>
              <div>{!! Mask::money($order->getTotalPayments(), true) !!}</div>
            </div>
          </div>

          <div class="d-flex justify-content-between mt-3">
            <div class="@if ($order->getTotalOwing() > 0) text-danger @else text-success @endif">
              <h5>Falta pagar</h5>
              <div>
                {!! Mask::money($order->getTotalOwing(), true) !!}
              </div>
            </div>
            <div>
              <h5>Quantidade</h5>
              <div>{{ $order->quantity }}</div>
            </div>
          </div>

          <div class="d-flex justify-content-between mt-5">
            <div>
              <h5>Data de produção</h5>
              <div>
                {{
                  $order->production_date
                    ? Helper::date($order->production_date, '%d/%m/%Y')
                    : '[não informado]' 
                }}
              </div>
            </div>
            <div>
              <h5>Data de entrega</h5>
              <div>
                {{ 
                  $order->delivery_date 
                    ? Helper::date($order->delivery_date, '%d/%m/%Y') 
                    : '[não informado]' 
                }}
              </div>
            </div>
          </div>

          <h4 class="font-weight-bold text-secondary mt-4 mb-3">Anexos</h4>
          <div class="d-flex justify-content-between flex-column flex-md-row">
            <a href="" data-attach="art">
              <i class="fas fa-images fa-fw mr-1"></i>Artes ({{ count($order->getPaths('art_paths')) }})
            </a>

            <a class="my-2 my-md-0" href="" data-attach="size">
              <i class="fas fa-images fa-fw mr-1"></i>Tamanhos ({{ count($order->getPaths('size_paths')) }})
            </a>

            <a href="" data-attach="payment_voucher">
              <i class="fas fa-file-alt fa-fw mr-1"></i>Comprovantes ({{ count($order->getPaths('payment_voucher_paths')) }})
            </a>
          </div>

          @include('orders.partials.payments-index')
        </div>
      </div>
    </div>
  </div>

  @if (! $order->isClosed())
    @modal([
      'id' => 'statusModal', 
      'title' => 'Alterar status',
      'headerClass' => 'bg-primary text-white font-weight-bold',
      'view' => 'orders.partials.change-status-form'
    ])
  @endif

  @role(['atendimento', 'gerencia'])
    @if (! $order->isClosed() || ($order->getTotalOwing() > 0))
      @modal([
        'id' => 'newPaymentModal', 
        'title' => 'Novo pagamento',
        'icon' => 'fas fa-dollar-sign',
        'modalDialogClass' => 'modal-dialog-centered',
        'headerClass' => 'bg-success text-white font-weight-bold',
        'view' => 'orders.partials.payment-form',
        'viewAttrs' => [
          'method' => 'POST'
        ]
      ])
    @endif  

    @modal([
        'id' => 'changePaymentModal',
        'title' => 'Alteração de pagamento',
        'modalDialogClass' => 'modal-dialog-centered',
        'headerClass' => 'bg-success text-white font-weight-bold'
      ])
  @endrole

  @modal([
    'id' => 'notesModal',
    'title' => 'Anotações sobre o pedido',
    'headerClass' => 'bg-primary text-white font-weight-bold',
    'icon' => 'fas fa-sticky-note',
    'view' => 'orders.partials.notes'
  ])

  @modal([
    'id' => 'fileViewerModal',
    'title' => 'Visualização de anexo',
    'modalDialogClass' => 'modal-dialog-centered modal-lg',
    'headerClass' => 'bg-dark text-white font-weight-bold'
  ])
  
  
@endsection

@push('script')
  <script src="{{ mix('js/partials/orders/show.js') }}"></script>
  <script src="{{ mix('js/partials/payments/index.js') }}"></script>
@endpush