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
						@if ($order->is_closed || $order->getTotalOwing() == 0)
							<span class="d-inline-block" 
								data-toggle="tooltip"
								title="Não é possível efetuar pagamento pois o pedido está @if ($order->is_closed)  fechado @else quitado @endif"> 

								<button style="pointer-events: none;" disabled="disabled" class="btn btn-outline-success d-block">
									<i class="fas fa-dollar-sign fa-fw mr-1"></i>Adicionar pagamento
								</button>
							</span>
						@else
							<a class="btn btn-outline-success d-block" href="#newPaymentModal" data-toggle="modal">
								<i class="fas fa-dollar-sign fa-fw mr-1"></i>Adicionar pagamento
							</a>
						@endif
					@else('design')
						<span class="d-inline-block w-100" tabindex="0" data-toggle="tooltip" title="Você não tem permissão para isso">
							<button style="pointer-events: none;" class="btn d-block w-100 btn-outline-success " disabled="disabled">
								<i class="fas fa-dollar-sign fa-fw mr-1"></i>Adicionar pagamento
							</button>
						</span>
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

						<a class="btn btn-outline-secondary d-block" onclick="event.preventDefault; document.querySelector('#toggleOrderForm').submit()">{{ $order->is_closed ? 'Reabrir pedido' : 'Fechar pedido' }}</a>
					@else('design')
						<span class="d-inline-block w-100" tabindex="0" data-toggle="tooltip" title="Você não tem permissão para isso">
							<button style="pointer-events: none;" class="btn btn-outline-secondary d-block w-100" disabled="disabled">
								{{ $order->is_closed ? 'Reabrir pedido' : 'Fechar pedido' }}
							</button>
						</span>
					@endrole
				</div>

				<div class="d-flex justify-content-between mt-3 mt-md-0">
					<a target="_blank" class="btn btn-primary" href="{{ route('orders.order-pdf', ['client' => $client, 'order' => $order]) }}">
						<i class="fas fa-file-invoice fa-fw mr-1"></i>Gerar relatório
					</a>

					@role(['atendimento', 'gerencia'])
						@if ($order->is_closed)
							<span tabindex="0" data-toggle="tooltip" title="Não é possível editar pois o pedido está fechado.">
								<button style="pointer-events: none;" class="btn btn-outline-primary mx-2" disabled="disabled">
									<i class="fas fa-edit fa-fw mr-1"></i>Editar
								</button>
							</span>
						@else
							<a class="btn btn-outline-primary mx-2" 
								href="{{ route('orders.edit', ['client' => $client, 'order' => $order]) }}">
								<i class="fas fa-edit fa-fw mr-1"></i>Editar
							</a>
						@endif
					@else
						<span tabindex="0" data-toggle="tooltip" title="Você não tem permissão para isso">
							<button style="pointer-events: none;" class="btn btn-outline-primary mx-2" disabled="disabled">
								<i class="fas fa-edit fa-fw mr-1"></i>Editar
							</button>
						</span>
					@endrole

					@role(['atendimento', 'gerencia'])
						<a class="btn btn-outline-danger" id="btnDeleteOrder" href="">
							<i class="fas fa-trash-alt fa-fw mr-1"></i>Excluir
						</a>
					@else
						<span tabindex="0" data-toggle="tooltip" title="Voce não tem permissão para isso">
							<button disabled="disabled" style="pointer-events: none;" class="btn btn-outline-danger">
								<i class="fas fa-trash-alt fa-fw mr-1"></i>Excluir
							</button>
						</span>
					@endrole

				</div>
			</div>
			<div class="card">
				<div class="card-header {{ $order->is_closed ?  'bg-secondary' : 'bg-primary' }} font-weight-bold text-white">
					<i class="fas fa-box-open fa-fw mr-1"></i>Pedido - {{ $order->code }} @if ($order->is_closed) - FECHADO @endif
				</div>

				<div class="card-body">
					<div class="mb-3 d-flex flex-row justify-content-between">
						<button data-toggle="modal" data-target="#notesModal" class="btn btn-outline-primary">
							<i class="fas fa-sticky-note fa-fw mr-1"></i>Anotações ({{ $order->notes->count() }})
						</button>

						@if ($order->is_closed)
							<span title="Não é possível alterar os status pois o pedido está fechado" 
								data-toggle="tooltip">
								<button style="pointer-events: none;" class="btn btn-outline-primary" disabled="disabled">
									Alterar status
								</button>
							</span>
						@else
							<button data-target="#statusModal" data-toggle="modal" class="btn btn-outline-primary">Alterar status</button>
						@endif

					</div>

					<div class="mb-4">
						<h4 class="font-weight-bold text-secondary">Detalhes do pedido ({{ $order->code }})</h4>
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
						<a href="" data-option="art">
							<i class="fas fa-images fa-fw mr-1"></i>Artes ({{ count($order->getPaths('art_paths')) }})
						</a>

						<a class="my-2 my-md-0" href="" data-option="size">
							<i class="fas fa-images fa-fw mr-1"></i>Tamanhos ({{ count($order->getPaths('size_paths')) }})
						</a>

						<a href="" data-option="payment_voucher">
							<i class="fas fa-file-alt fa-fw mr-1"></i>Comprovantes ({{ count($order->getPaths('payment_voucher_paths')) }})
						</a>
					</div>

					<h4 class="font-weight-bold text-secondary mt-4 mb-3">Pagamentos</h4>
					<div class="d-flex flex-column">
						<ul class="list-group list-group-flush">
							@forelse($order->payments->reverse() as $payment)
								<li class="list-group-item">
									<strong>{{ Mask::money($payment->value) }}</strong> em {{ Helper::date($payment->date, '%d/%m/%Y') }}
									@if (! empty($payment->note))
										- 
										<a onclick="event.preventDefault()" href="" data-toggle="tooltip" title="{{ $payment->note }}">(ver anotação)</a>
									@endif
								</li>
							@empty
								<li class="list-group-item text-center">
									<h5 class="text-secondary">Nenhum pagamento feito ainda</h5>
								</li>
							@endforelse
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>


	@if (! $order->is_closed)
		@include('orders._change-status-modal')
	@endif

	@role(['atendimento', 'gerencia'])
		@if (! $order->is_closed || ($order->getTotalOwing() > 0))
			@include('orders._new-payment-modal')
		@endif	
	@endrole

	@include('orders._notes-modal')
	@include('orders._file-viewer-modal')
@endsection

@push('script')
	<script src="{{ mix('js/partials/orders/show.js') }}"></script>
@endpush