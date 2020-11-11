<div class="form-group {{ $formGroupClass ?? '' }}">
	@isset($label)
		<label for="{{ $id ?? '' }}" class="{{ $labelClass ?? '' }}">{{ $label }}</label>
	@endisset

	@if (isset($optional) && $optional)
		<small class="text-secondary">(opcional)</small>
	@endif

	<div class="custom-file">
		<input type="file" 
			class="custom-file-input {{ $class ?? '' }}" 
			@isset ($id) id="{{ $id }}" @endisset 
			@isset ($name) name="{{ $name }}" @endisset
			@isset ($accept) accept="{{ $accept }}" @endisset 
			@if (isset($multiple) && $multiple) multiple="multiple" @endif
		> 

		<label class="custom-file-label">{{ $fileLabel }}</label>
	</div>
</div>