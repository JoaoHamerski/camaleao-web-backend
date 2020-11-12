<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label" aria-hidden="true">
  <div class="modal-dialog {{ $modalDialogClass ?? '' }}">
    <div class="modal-content">
      <div class="modal-header {{ $headerClass ?? '' }}">
        <h5 class="modal-title font-weight-bold" id="{{ $id }}Label">
          @isset($icon)
            <i class="{{ $icon }} fa-fw mr-2"></i>
          @endisset

          {{ $title ?? '' }}
        </h5>

        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body {{ $bodyClass ?? '' }}">
        @isset($view)
          @include($view, $viewAttrs ?? [])
        @endisset
      </div>
    </div>
  </div>
</div>