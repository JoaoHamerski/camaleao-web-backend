@extends('layout')

@section('title', 'Pedido - ' . $order->code)

@section('content')
  <div class="row mt-5 ">
    <div class="col-md-3">
      <a class="btn btn-outline-primary mb-2" href="{{ $client->path() }}">
        <i class="fas fa-arrow-circle-left fa-fw mr-1"></i>Voltar
      </a>

      @include('clients._client-card')
    </div>

    <div class="col-md-9 mt-3 mt-md-0 orders">
      <div class="d-flex flex-column flex-md-row justify-content-between mb-2">
        <div class="mt-3 mt-md-0">
          @include('orders.components.btn-add-payment')
        </div>

        <div class="mt-3 mt-md-0">
          @include('orders.components.btn-close-order')
        </div>

        <div class="d-flex justify-content-between mt-3 mt-md-0">
          @include('orders.components.btn-generate-report')
          
          @include('orders.components.btn-edit-order')

          @include('orders.components.btn-delete-order')
        </div>
      </div>
      
      <x-card
        :header-color="[
          'secondary' => $order->isClosed(),
          'warning' => $order->isPreRegistered(),
          'primary'
        ]"
        icon="fas fa-box-open"
      >
        <x-slot name="header">
          Pedido - {{ $order->name ?? $order->code }}

          @if ($order->isClosed())
            - FECHADO EM {{ Helper::date($order->closed_at, '%d/%m/%Y') }}
          @elseif ($order->isPreRegistered())
            - PEDIDO EM PRÉ-REGISTRO
          @endif
        </x-slot>
        
        <x-slot name="body">
          @if ($order->isPreRegistered())
            <div class="text-center text-secondary mb-3">
              @if ($order->getReminder())
                <div class="text-dark">
                  <strong>LEMBRETE: </strong> {{ $order->getReminder()->text }}
                </div>
              @endif
    
              <small>Use a opção <strong>editar</strong> para preencher os dados restantes do pedido</small>
            </div>
          @endif
  
          <div class="mb-3 d-flex flex-row justify-content-between">
            <button 
              data-toggle="modal" 
              data-target="#notesModal" 
              class="btn btn-outline-primary"
            >
              <i class="fas fa-sticky-note fa-fw mr-1"></i>Anotações ({{ $order->notes->WhereNull('is_reminder')->count() }})
            </button>
            
            @include('orders.components.btn-change-status')
          </div>
  
          <div class="mb-4">
            <h5 class="font-weight-bold text-secondary">
              &bull; Detalhes do pedido 
            </h5>
            <div class="font-weight-bold">
              @if ($order->name) {{ $order->name }} - @endif 
              <span class="text-primary">{{ $order->code }}</span>
            </div>
          </div>
  
          <div class="d-flex justify-content-between">
            <div>
              <small class="font-weight-bold text-secondary">Status</small>
              <h5 
                @class([
                  'font-weight-bold',
                  'text-success' => $order->status->id == 8,
                  'text-warning' => $order->status->id != 8
                ])
              >{{ $order->status->text }}</h5>
            </div>
  
            <div>
              <small class="font-weight-bold text-secondary">Quantidade</small>
              <h5>{{ $order->quantity ?? 'N/A' }}</h5>
            </div>
          </div>
          
          @if (! $order->clothingTypes->isEmpty())
            <div class="mt-3">
              <small class="text-secondary font-weight-bold">Tipos de roupa</small>
              <div class="table-responsive mt-2">
                <table class="table table-sm">
                  <thead>
                    <tr>
                      <th>TIPO</th>
                      <th>QUANTIDADE</th>
                      <th>VALOR UNIT.</th>
                      <th >TOTAL</th>
                    </tr>
                  </thead>
            
                  <tbody>
                    @foreach ($order->clothingTypes()->orderBy('order')->get() as $type)
                      <tr>
                        <td nowrap>{{ $type->name }}</td>
                        <td>{{ $type->pivot->quantity }}</td>
                        <td>{{ Mask::money($type->pivot->value) }}</td>
                        <td>{{ Mask::money($type->totalValue()) }}</td>
                      </tr>
                    @endforeach
    
                    <tr @if($order->discount == 0) class="table-primary" @endif>
                      <td nowrap class="font-weight-bold">
                        @if($order->discount == 0) VALOR FINAL @else TOTAL @endif
                      </td>
                      <td class="font-weight-bold" colspan="2">{{ $order->quantity }}</td>
                      <td class="font-weight-bold">{{ Mask::money($order->totalClothingsValue()) }}</td>
                    </tr>
    
                    @if ($order->discount > 0.01)
                      <tr class="font-weight-bold">
                        <td colspan="3">DESCONTO</td>
                        <td class="minus-value">{{ Mask::money($order->discount) }}</td>
                      </tr>
                      <tr class="font-weight-bold table-primary">
                        <td nowrap colspan="3">VALOR FINAL</td>
                        <td>{{ Mask::money($order->price) }}</td>
                      </tr>
                    @endif
                  </tbody>
                </table>
              </div>
            </div>
          @else
            <div class="d-flex justify-content-between">
              <div>
                <small class="font-weight-bold text-dark">Valor total</small>
                <h5>{!! Mask::money($order->price, true) !!}</h5>
              </div>
            </div>
          @endif
  
          <div class="d-flex justify-content-around mt-4">
            <div class="text-success">
              <small class="font-weight-bold text-dark">Total pago</small>
              <h5>{!! Mask::money($order->getTotalPayments(), true) !!}</h5>
            </div>
  
            <div>
              <small class="font-weight-bold text-dark">Falta pagar</small>
              <h5 
                @class([
                  'text-danger' => $order->getTotalOwing() > 0,
                  'text-success' => $order->getTotalOwing() == 0
                ])
              >
                {!! Mask::money($order->getTotalOwing(), true) !!}
              </h5>
            </div>
          </div>
  
          <div class="d-flex justify-content-between mt-4">
            <div>
              <small class="font-weight-bold text-secondary">Data de produção</small>
              <div>
                {{
                  $order->production_date
                    ? Helper::date($order->production_date, '%d/%m/%Y')
                    : 'N/A' 
                }}
              </div>
            </div>
            <div>
              <small class="font-weight-bold text-secondary">Data de entrega</small>
              <div>
                {{ 
                  $order->delivery_date 
                    ? Helper::date($order->delivery_date, '%d/%m/%Y') 
                    : 'N/A' 
                }}
              </div>
            </div>
          </div>
  
          <h5 class="font-weight-bold text-secondary mt-4 mb-3">
            &bull; Anexos
          </h5>

          <div class="d-flex justify-content-between flex-column flex-md-row">
            <a href="" data-attach="art">
              <i class="fas fa-images fa-fw mr-1"></i>
              Artes ({{ count($order->getPaths('art_paths')) }})
            </a>
  
            <a class="my-2 my-md-0" href="" data-attach="size">
              <i class="fas fa-images fa-fw mr-1"></i>
              Tamanhos ({{ count($order->getPaths('size_paths')) }})
            </a>
  
            <a href="" data-attach="payment_voucher">
              <i class="fas fa-file-alt fa-fw mr-1"></i>
              Comprovantes ({{ count($order->getPaths('payment_voucher_paths')) }})
            </a>
          </div>
  
          @include('orders.partials.payments')
        </x-slot>
      </x-card>
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