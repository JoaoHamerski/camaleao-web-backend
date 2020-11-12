<div class="form-group {{ $formGroupClass ?? '' }}">
	@isset($label)
		<label class="{{ $labelClass ?? '' }}">{{ $label }}</label>
	@endisset

	@foreach($inputs as $input)
	<div class="custom-control custom-radio custom-control-inline">
		<input @if(isset($input['checked']) && $input['checked']) checked="checked" @endif
			type="radio" 
			id="{{ $input['id'] }}" 
			name="{{ $name }}" 
			class="custom-control-input" 
			value="{{ $input['value'] }}">

  		<label class="custom-control-label" 
  		for="{{ $input['id'] }}">{{ $input['label'] }}</label>
	</div>
	@endforeach
</div>