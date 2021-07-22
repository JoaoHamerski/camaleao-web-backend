<a target="_blank" 
  class="btn btn-primary" 
  href="{{ route(
    'orders.order-pdf', [
      'client' => $client, 
      'order' => $order
    ]) }}"
>
  <i class="fas fa-file-invoice fa-fw mr-1"></i>Gerar relatório
</a>