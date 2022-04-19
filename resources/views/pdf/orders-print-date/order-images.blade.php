<div>
  <div class="mt-2 text-center">
    @include('pdf.orders-print-date.order-image', [
      'image' => [
        'field' => 'art_paths',
        'label' => 'IMAGEM DA ARTE'
      ]
    ])
  </div>
  <div class="mt-2 text-center">
    @include('pdf.orders-print-date.order-image', [
      'image' => [
        'field' => 'size_paths',
        'label' => 'IMAGEM DO TAMANHO'
      ]
    ])
  </div>
</div>
