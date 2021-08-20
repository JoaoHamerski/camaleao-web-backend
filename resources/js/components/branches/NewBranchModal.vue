<template>
  <AppModal
    id="newBranchModal" 
    ref="modal"
    modal-dialog-class="modal-dialog-centered"
    modal-header-class="bg-success text-white"
  >
    <template #header>
      <h5 class="font-weight-bold mb-0">
        <i class="fas fa-plus fa-fw m-r1" />  Adicione uma nova filial
      </h5>
    </template>

    <template #body>
      <BranchForm @created="onCreated" />
    </template>
  </AppModal>
</template>

<script>
import BranchForm from './BranchForm'

export default {
    components: {
        BranchForm
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