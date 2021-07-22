<div>
  <small class="text-secondary">
    @if (Request::query('ordem') == 'mais_antigo')
      Exibindo pedidos por <strong>ordem de cadastro mais antigos primeiros</strong>, incluindo pedidos fechados.
    @elseif (Request::query('ordem') == 'mais_recente')
      Exibindo pedidos por <strong>ordem de cadastro mais recente primeiros</strong>, incluindo pedidos fechados.
    @elseif (Request::query('ordem') == 'data_de_entrega')
      Exibindo pedidos por <strong>ordem de data de entrega mais antiga primeiro</strong>, apenas pedidos em aberto
      <br>
      (pedidos sem data de entrega informada ficam por último).
    @elseif (Request::query('filtro') == 'pre-registro')
      <span class="text-danger font-weight-bold">Pedidos que precisam ter seus dados completados.</span>
    @else
      Exibindo pedidos por <strong>ordem de cadastro mais antigo primeiros</strong>, apenas pedidos em aberto.
    @endif
  </small>
</div>