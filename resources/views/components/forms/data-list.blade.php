<datalist id="{{ $id }}">
	@foreach($items as $item)
		<option value="{{ $item }}"></option>
	@endforeach
</datalist>