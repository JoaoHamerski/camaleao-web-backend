<strong>{{ $activity->causer->name }}</strong>

@if (\Str::contains($activity->description, 'data-event="updated"'))
	alterou os dados de pagamento do pedido
	<strong>
		<a target="_blank" href="{{ $activity->subject->order->path() }}">
			{{ $activity->subject->order->code }}
		</a>
	</strong>
@elseif(\Str::contains($activity->description, 'data-event="created"'))
	adicionou um pagamento de <strong>{{ Mask::money($activity->subject->value) }}</strong>
	para o pedido
	<strong> 
		<a target="_blank" href="{{ $activity->subject->order->path() }}">
			{{ $activity->subject->order->code }}
		</a>
	</strong>
@endif
