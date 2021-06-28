<template>
  <AppModal id="editCitiesModal"
    ref="modal"
    modalDialogClass="modal-dialog-centered"
    modalHeaderClass="bg-primary"
  >
    <template #header>
      <h5 class="text-white font-weight-bold mb-0">
        <i class="fas fa-edit fa-fw mr-1"></i>Alterar estado das cidades
      </h5>
    </template>

    <template #body>
      <CitiesForm @updated="onUpdate" ref="citiesForm" />
    </template>
  </AppModal>
</template>

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