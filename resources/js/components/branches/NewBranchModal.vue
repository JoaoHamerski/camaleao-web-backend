<template>
  <AppModal ref="modal" 
    id="newBranchModal"
    modalDialogClass="modal-dialog-centered"
    modal-header-class="bg-success text-white"
  >
    <template #header>
      <h5 class="font-weight-bold mb-0">
        <i class="fas fa-plus fa-fw m-r1"></i>  Adicione uma nova filial
      </h5>
    </template>

    <template #body>
      <NewBranchForm @created="onCreated" />
    </template>
  </AppModal>
</template>

<script>
  import NewBranchForm from './NewBranchForm'

  export default {
    components: {
      NewBranchForm
    },
    methods: {
      onCreated() {
        this.$emit('refresh')

        $(this.$refs.modal.$el).modal('hide')

        this.$toast.success('Filial cadastrada')
      }
    }
  }
</script>