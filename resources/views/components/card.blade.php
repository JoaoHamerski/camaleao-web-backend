<div class="card {{ $attributes['class'] }}">
    <div class="card-header bg-{{ $headerColor }} text-white position-relative">
        @if ($isCollapsed)
        <a href="#collapse-card-{{ $collapseId }}" class="stretched-link collapsed" data-toggle="collapse" aria-expanded="true"></a>
            
        <div class="card-collapse">
        @endif
        
        @if (! empty($headerUrl))
            <a href="{{ $headerUrl }}" class="stretched-link"></a>
        @endif
            <div class="font-weight-bold text-white">
                @if ($icon) <i class="{{ $icon }} fa-fw mr-1"></i> @endif{{ $header }}
            </div>

        @if ($isCollapsed)
            <div class="collapse-icon">
                <i class="fas fa-caret-down fa-fw fa-2x"></i>
            </div>
        </div>
        @endif
    </div>
    
    @if ($isCollapsed)
        <div id="collapse-card-{{ $collapseId }}" class="collapse">
    @endif

    <div class="card-body {{ $hasBodyPadding === false ? 'px-0' : '' }}">
        {{ $body }}
    </div>
    
    @if ($isCollapsed)
        </div>
    @endif

    @isset ($footer)
        <div class="card-footer">
            {{ $footer }}
        </div>
    @endif
</div>