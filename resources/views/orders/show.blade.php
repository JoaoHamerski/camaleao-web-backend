@extends('layout')

@section('title', 'Pedido - ' . $order->code)

@section('content')
	<div class="row mt-5">
		<div class="col-md-3">
			<a class="btn btn-outline-primary" href="{{ $client->path() }}">
				<i class="fas fa-arrow-circle-left fa-fw mr-1"></i>Voltar
			</a>	
		</div>

		<div class="col-md-9">
			<div class="d-flex flex-row justify-content-between">
				<div>
					<a class="btn btn-outline-success @if ($order->is_closed || ($order->getTotalOwing() == 0)) disabled @endif " href="#newPaymentModal" data-toggle="modal">
						<i class="fas fa-dollar-sign fa-fw mr-1"></i>Adicionar pagamento
					</a>
				</div>
				<div>
					<form id="toggleOrderForm" method="POST" class="d-none" 
						action="{{ 
							route('orders.toggleOrder', [
								'client' => $client, 
								'order' => $order
							]) 
						}}">
							@csrf
					</form>

					<a class="btn btn-outline-secondary" onclick="event.preventDefault; document.querySelector('#toggleOrderForm').submit()">{{ $order->is_closed ? 'Reabrir pedido' : 'Fechar pedido' }}</a>
				</div>

				<div>
					<a target="_blank" class="btn btn-primary" href="{{ route('orders.order-pdf', ['client' => $client, 'order' => $order]) }}">
						<i class="fas fa-file-invoice fa-fw mr-1"></i>Gerar relatório
					</a>

					<a class="btn btn-outline-primary @if ($order->is_closed) disabled @endif" 
						href="{{ route('orders.edit', ['client' => $client, 'order' => $order]) }}">
						<i class="fas fa-edit fa-fw mr-1"></i>Editar
					</a>

					<a class="btn btn-outline-danger" id="btnDeleteOrder" href="">
						<i class="fas fa-trash-alt fa-fw mr-1"></i>Excluir
					</a>
				</div>
			</div>
		</div>
	</div>

	<div class="row mt-2">
		<div class="col-md-3">
			@include('clients._client-card')
		</div>

		<div class="col-md-9">
			<div class="card">
				<div class="card-header {{ $order->is_closed ?  'bg-secondary' : 'bg-primary' }} font-weight-bold text-white">
					<i class="fas fa-box-open fa-fw mr-1"></i>Pedido - {{ $order->code }} @if ($order->is_closed) - FECHADO @endif
				</div>

				<div class="card-body">
					<div class="mb-3 d-flex flex-row justify-content-between">
						<button data-toggle="modal" data-target="#notesModal" class="btn btn-outline-primary">
							<i class="fas fa-sticky-note fa-fw mr-1"></i>Anotações ({{ $order->notes->count() }})
						</button>

						<button @if($order->is_closed) disabled="disabled" @endif data-target="#statusModal" data-toggle="modal" class="btn btn-outline-primary">Alterar status</button>
					</div>

					<div class="mb-4">
						<h4 class="font-weight-bold text-secondary">Detalhes do pedido ({{ $order->code }})</h4>
					</div>

					<div class="d-flex justify-content-between">
						<div class="@if($order->status->id == 8) text-success @else text-warning @endif">
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
					<div class="d-flex justify-content-between">
						<a href="" data-option="art">
							<i class="fas fa-images fa-fw mr-1"></i>Artes ({{ count($order->getPaths('art_paths')) }})
						</a>

						<a href="" data-option="size">
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

	@if (! $order->is_closed || ($order->getTotalOwing() > 0))
		@include('orders._new-payment-modal')
	@endif	

	@include('orders._notes-modal')
	@include('orders._file-viewer-modal')
@endsection

@push('script')
	<script src="{{ mix('js/partials/show-order.js') }}"></script>
@endpush