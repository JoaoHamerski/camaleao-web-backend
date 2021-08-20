<template>
  <AppModal
    id="pendenciesModal"
    ref="modal"
    modal-header-class="bg-warning"
    modal-dialog-class="modal-dialog-centered modal-dialog-scrollable"
  >
    <template #header>
      <h5 class="text-white font-weight-bold mb-0">
        Pendências em aprovações de pagamentos
      </h5>
    </template>

    <template #body>
      <PendenciesList
        ref="pendenciesList" 
        @load-pendencies="onLoadPendencies" 
      />
    </template>

    <template #footer>
      <button
        class="btn btn-light"
        data-dismiss="modal"
      >
        Fechar
      </button>
    </template>
  </AppModal>
</template>

<script>
import PendenciesList from './PendenciesList'

export default {
    components: {
        PendenciesList
    },
    mounted() {
        this.$on('refresh-pendencies', () => {
            this.$refs.pendenciesList.$emit('refresh-pendencies')
        })
    },
    methods: {
        onLoadPendencies(date) {
            $(this.$refs.modal.$el).modal('hide')
            this.$emit('load-pendencies', date)
        }
    }
}
</script>