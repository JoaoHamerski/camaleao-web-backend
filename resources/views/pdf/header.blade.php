<div class="header">
  <div class="header-logo">
    <img
      class="header-camaleao-logo"
      src="{{ FileHelper::imageToBase64(
        public_path('/images/logo.png')
        ) }}"
    />
  </div>

  @if (!empty($title))
  <div class="header-title">
    {{ $title }}
  </div>
  @endif

  @if(!empty($subtitle))
  <div class="header-subtitle">
    {{ $subtitle }}
  </div>
  @endif
</div>
