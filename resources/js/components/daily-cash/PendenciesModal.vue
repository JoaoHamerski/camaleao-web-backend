<template>
  <AppModal id="pendenciesModal"
    ref="modal"
    modalHeaderClass="bg-warning"
    modalDialogClass="modal-dialog-centered modal-dialog-scrollable"
  >
    <template #header>
      <h5 class="text-white font-weight-bold mb-0">
        Pendências em aprovações de pagamentos
      </h5>
    </template>

    <template #body>
      <PendenciesList ref="pendenciesList" 
        @load-pendencies="onLoadPendencies" 
      />
    </template>

    <template #footer>
      <button class="btn btn-light" data-dismiss="modal">Fechar</button>
    </template>
  </AppModal>
</template>

<script>
  import PendenciesList from './PendenciesList'

  export default {
    components: {
      PendenciesList
    },
    methods: {
      onLoadPendencies(date) {
        $(this.$refs.modal.$el).modal('hide')
        this.$emit('load-pendencies', date)
      }
    },
    mounted() {
      this.$on('refresh-pendencies', () => {
        this.$refs.pendenciesList.$emit('refresh-pendencies')
      })
    }
  }
</script>