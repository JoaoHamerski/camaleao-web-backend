
@if (\Str::contains($activity->description, 'data-event="created"'))
	<strong>{{ $activity->causer->name }}</strong>
	cadastrou uma despesa 
	<strong>"{{ $activity->subject->description }}"</strong>
	com a data de 
	<strong>{{ Helper::date($activity->subject->date, '%d/%m/%Y') }}</strong>
	no valor de
	<strong>{{ Mask::money($activity->subject->value) }}</strong>
@elseif(\Str::contains($activity->description, 'data-event="updated"'))
	{!! $activity->description !!}
@endif
