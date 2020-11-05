<li class="no-gutters list-group-item d-flex flex-column flex-sm-row justify-content-between">
	<div class="col-sm-10">
		@if ($activity->subject && $activity->causer)
			@include('activities.partials-list.' . $viewName)
		@else
			{!! $activity->description !!}
		@endif

		@if (\Str::contains($activity->description, ['alterou']))
			<div>
				<a data-toggle="modal" href="#changesModal{{ $activity->id }}">(ver alterações)</a>
					@include('activities._modal-changes', [
						'index' => $activity->id,
						'changes' => $activity->changes,
						'subject_type' => $activity->subject_type
					])
			</div>
		@endif

	</div>

	<div class="col-sm-2 d-flex flex-row flex-sm-column text-left text-sm-right mt-2 mt-sm-0">
		<div class="mr-2 mr-sm-0 text-nowrap">
			<strong>{{ Helper::date($activity->created_at, '%d/%m/%Y') }}</strong>
		</div>
		<div>
			<strong>{{ Helper::date($activity->created_at, '%H:%M:%S') }}</strong>
		</div>
	</div>
</li>