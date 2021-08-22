<template>
  <AppModal
    id="editCityModal"
    ref="modal"
    modal-dialog-class="modal-dialog-centered"
    modal-header-class="bg-primary"
  >
    <template #header>
      <h5 class="text-white font-weight-bold mb-0">
        <i class="fas fa-edit fa-fw mr-1" />Editar cidade
      </h5>
    </template>

    <template #body>
      <CityForm
        ref="cityForm"
        :is-edit="true" 
        @updated="onUpdate"
      />
    </template>
  </AppModal>  
</template>

<script>
import CityForm from './CityForm'

export default {
  components: {
    CityForm
  },
  methods: {
    onUpdate() {
      $(this.$refs.modal.$el).modal('hide')
      this.$emit('updated')
    }
  }
}
</script>