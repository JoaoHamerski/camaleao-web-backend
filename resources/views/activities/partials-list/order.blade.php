<strong>{{ $activity->causer->name }}</strong>

@if ($activity->subject->client !== null)
@if (\Str::contains($activity->description, 'data-event="updated"'))
	alterou os dados do pedido
	<strong>
		<a target="_blank" href="{{ $activity->subject->path() }}">
			{{ $activity->subject->code }}
		</a>
	</strong>
	do cliente
	<strong>
		<a target="_blank" href="{{ $activity->subject->client->path() }}">
			{{ $activity->subject->client->name }}
		</a>
	</strong>
@elseif(\Str::contains($activity->description, 'data-event="created"'))
	cadastrou o pedido
	<strong>
		<a target="_blank" href="{{ $activity->subject->path() }}">
			{{ $activity->subject->code }}
		</a>
	</strong>
	para o cliente
	<strong>
		<a target="_blank" href="{{ $activity->subject->client->path() }}">
			{{ $activity->subject->client->name }}
		</a>
	</strong>
@endif
@else
@if (\Str::contains($activity->description, 'data-event="updated"'))
	alterou os dados do pedido
	<strong>
		<a target="_blank" href="{{ $activity->subject->path() }}">
			{{ $activity->subject->code }}
		</a>
	</strong>
@elseif(\Str::contains($activity->description, 'data-event="created"'))
	pre-registrou um <a class="font-weight-bold" target="_blank" href="{{ $activity->subject->path() }}">pedido</a>
@endif
@endif
