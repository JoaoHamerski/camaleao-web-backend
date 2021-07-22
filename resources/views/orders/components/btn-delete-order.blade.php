<span class="d-inline-block"
  @role('design')
    content="Você não tem permissão para isso"
    v-tippy="{arrow: true, duration: 150, placement: 'bottom'}"
  @endrole
>
  <button class="btn btn-outline-danger" id="btnDeleteOrder"
    @role('design')
      disabled="disabled"
    @endrole
  >
    <i class="fas fa-trash-alt fa-fw mr-1"></i>Excluir
  </button>
</span>