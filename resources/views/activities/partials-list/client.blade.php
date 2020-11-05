<strong>{{ $activity->causer->name }}</strong>

@if (\Str::contains($activity->description, 'data-event="updated"')) 
	alterou os dados do cliente
@elseif(\Str::contains($activity->description, 'data-event="created"'))
	cadastrou o cliente
@endif

<strong>
<a target="_blank" href="{{ $activity->subject->path() }}">{{ $activity->subject->name }}</a>
</strong>

