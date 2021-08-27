<script>
import CitiesForm from './CitiesForm'

export default {
  components: {
    CitiesForm
  },
  methods: {
    onUpdate() {
      $(this.$refs.modal.$el).modal('hide')
      this.$emit('updated')
    }
  }
}
</script>

<template>
  <AppModal
    id="editCitiesModal"
    ref="modal"
    modal-dialog-class="modal-dialog-centered"
    modal-header-class="bg-primary"
  >
    <template #header>
      <h5 class="text-white font-weight-bold mb-0">
        <i class="fas fa-edit fa-fw mr-1" />Alterar estado das cidades
      </h5>
    </template>

    <template #body>
      <CitiesForm
        ref="citiesForm"
        @updated="onUpdate"
      />
    </template>
  </AppModal>
</template>
