	<div>

		<h5 class="text-center my-3">
			Pagamento de 
			<strong>{{ Mask::money($payment->value) }}</strong> 
			em 
			<strong>{{ Helper::date($payment->date, '%d/%m/%Y') }}</strong>
		</h5>

		@if ($payment->note)
			<div class="text-center mb-1">
				<small class="text-secondary">{{ $payment->note }}</small>
			</div>
		@endif

		<form action="">
			<div class="form-group">
				<label class="font-weight-bold" for="payment_via_id">Alterar via</label>
				<select class="custom-select" name="payment_via_id" id="payment_via_id">
					<option value="">Selecione a via</option>
					@foreach($vias as $via)
						<option @if($payment->via && $via->id == $payment->via->id) selected="selected" @endif value="{{ $via->id }}">{{ $via->name }}</option>
					@endforeach
				</select>
			</div>

			<div class="form-group">
				<label class="font-weight-bold" for="note">Observação</label>
				<small class="text-secondary">(opcional)</small>
				<input type="text" 
					placeholder="Anotação extra sobre o pagamento..." 
					class="form-control"
					name="note" 
					value="{{ $payment->note ?? '' }}">
			</div>

			<div>
				<button data-id="{{ $payment->id }}" id="btnChangePayment" class="btn btn-primary">Salvar</button>
			</div>
		</form>

	</div>