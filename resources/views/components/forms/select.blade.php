<div class="form-group {{ $formGroupClass ?? '' }}">
	@isset($label)
		<label for="{{ $id ?? '' }}" class="font-weight-bold">{{ $label }}</label>
	@endisset

	<select class="custom-select" 
		@isset ($name) name="{{ $name }}" @endisset 
		@isset ($id ) id="{{ $id }}" @endisset
		@isset($attributes) {{ Helper::renderAttributes($attributes) }} @endisset
	>

		<option @if ($itemKeyToMatch == null) selected="selected" @endif value="">{{ $defaultOptionText }}</option>
		
		@foreach($items as $item)
			<option @if($item->{$key ?? 'id'} == $itemKeyToMatch) selected="selected" @endif value="{{ $item->{$key ?? 'id'} }}">{{ $item->{$itemAttribute} }}</option>
		@endforeach
	</select>
</div>