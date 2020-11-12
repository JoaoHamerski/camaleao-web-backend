<div class="form-group @isset ($formGroupClass) {{ $formGroupClass }} @endisset">
	@isset ($label)
		<label for="{{ $id ?? '' }}" class="{{ $labelClass ?? '' }}">
			{{ $label }}
		</label>
		
		@if (isset($optional) && $optional)
			<small class="text-secondary">(opcional)</small>
		@endif
	@endisset

	@isset($inputGroup)
		<div class="input-group {{ $inputGroup['class'] ?? '' }}">
	@endisset

	<input class="form-control" 
		type="{{ $type ?? 'text' }}"
		@isset ($id) id="{{ $id }}" @endisset 
		@isset ($name) name="{{ $name }}" @endisset
		@isset ($value) value="{{ $value }}" @endisset
		@isset ($placeholder) placeholder="{{ $placeholder }}" @endisset 
		@isset ($attributes) {!! Helper::renderAttributes($attributes) !!} @endisset/>

	@isset($inputGroup)
		@isset($inputGroup['btnAppend'])
			<div class="input-group-append">
				<button class="{{ $inputGroup['btnAppend']['class'] ?? '' }}"
			 		@isset($inputGroup['btnAppend']['id']) 
					id="{{ $inputGroup['btnAppend']['id'] }}"
					@endisset
					@isset($inputGroup['btnAppend']['attributes'])
					{{ Helper::renderAttributes($inputGroup['btnAppend']['attributes']) }} 
					@endisset
				>

				@isset($inputGroup['btnAppend']['icon'])
					<i class="{{ $inputGroup['btnAppend']['icon'] }} fa-fw mr-1"></i>
				@endisset
					{{ $inputGroup['btnAppend']['text'] }}
				</button>
			</div>	
		@endisset
		</div>
	@endisset

	@isset($helpMessage)
		<small class="text-secondary">{{ $helpMessage }}</small>
	@endisset
</div>