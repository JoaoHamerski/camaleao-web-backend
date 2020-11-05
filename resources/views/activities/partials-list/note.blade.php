<strong>{{ $activity->causer->name }}</strong>

@if (\Str::contains($activity->description, 'data-event="created"')) 
	adicionou a anotação <strong>"{{ $activity->subject->text }}"</strong> 
	ao pedido 
	<strong>
		<a target="_blank" href="{{ $activity->subject->order->path() }}">{{ $activity->subject->order->code }}</a>
	</strong>
@endif
