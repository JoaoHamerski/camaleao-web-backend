@push('styles')
  <style>
    .img-thumbnail-title {
      width: 40%;
      border: 3px solid white;
      outline: 1px solid #dee2e6;
      font-size: .9rem;
      font-weight: bold;
      color: rgba(0, 0, 0, 0.795);
    }

    .img-production {
      max-height: 350px;
    }
  </style>
@endpush

@if ($order->{$image['field']})
<div class="bg-secondary img-thumbnail-title px-3 py-1 mx-auto">
  <span>{{ $image['label'] }}</span>
</div>
<img class="img-fluid img-thumbnail img-production" src="{{
  FileHelper::imageToBase64(
    Helper::getPublicPathFromUrl($order->{$image['field']}[0])
    )
  }}"
>
@else
<div class="img-thumbnail text-center text-secondary">
  <div class="py-4 fw-bold">[SEM {{ $image['label'] }}]</div>
</div>
@endif
