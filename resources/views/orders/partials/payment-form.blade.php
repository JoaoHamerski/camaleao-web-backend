<form>
	@if ($method == 'POST')
		@input([
			'label' => 'Valor',
			'labelClass' => 'font-weight-bold',
			'id' => 'value',
			'name' => 'value'
		])

		@input([
			'label' => 'Data de pagamento',
			'labelClass' => 'font-weight-bold',
			'placeholder' => 'dd/mm/aaaa',
			'id' => 'date',
			'name' => 'date',
			'inputGroup' => [
				'btnAppend' => [
					'text' => 'Hoje',
					'class' => 'btn btn-outline-primary btn-today'
				]
			]
		])
	@endif

	@if ($method == 'PATCH')
		<h5 class="text-center my-3">
			Pagamento de 
			<strong>{{ Mask::money($payment->value) }}</strong> 
			em 
			<strong>{{ Helper::date($payment->date, '%d/%m/%Y') }}</strong>
		</h5>

		@if ($payment->note)
			<div class="text-center mb-1">
				<div class="text-secondary">{{ $payment->note }}</div>
			</div>
		@endif
	@endif

	@select([
		'label' => 'Via',
		'id' => 'payment_via_id',
		'name' => 'payment_via_id',
		'items' => $vias,
		'itemAttribute' => 'name',
		'itemKeyToMatch' => $method == 'PATCH' && $payment->via ? $payment->via->id : null, 
		'defaultOptionText' => 'Selecione a via'
	])

	@input([
		'label' => 'Observação',
		'labelClass' => 'font-weight-bold',
		'id' => 'note',
		'name' => 'note',
		'placeholder' => 'Anotação extra sobre o pagamento',
		'value' => $method == 'PATCH' ? $payment->note : ''
	])

	<div class="d-flex justify-content-between">
	@if ($method == 'POST')
        <button id="btnAddPayment" data-id="{{ $order->id }}" type="submit" class="btn btn-primary">
        	Salvar
        </button>
	@else
		<button data-id="{{ $payment->id }}" id="btnChangePayment" class="btn btn-primary">
			Atualizar
		</button>
	@endif

	<button type="button" class="btn btn-light" data-dismiss="modal">Fechar</button>

	</div>
</form>