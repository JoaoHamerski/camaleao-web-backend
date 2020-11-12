@isset($title)
<span class="d-inline-block {{ $wrapperClass ?? '' }}" 
    data-toggle="tooltip"
    title="{{ $title }}"> 
@endisset
	<button class="{{ $class ?? '' }}"
		@isset($title)
		style="pointer-events: none;"
		disabled="disabled" 
		@endisset
	>
		@isset($icon) <i class="{{ $icon }} fa-fw"></i> @endisset
		{{ $text ?? '' }}
	</button>
@isset($title)
</span>
@endisset