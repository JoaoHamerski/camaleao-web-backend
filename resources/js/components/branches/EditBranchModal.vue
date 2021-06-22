<template>
  <AppModal ref="modal" 
    id="editBranchModal"
    modalDialogClass="modal-dialog-centered"
    modalHeaderClass="bg-primary text-white"
  >
    <template #header>
      <h5 class="font-weight-bold mb-0">
        <i class="fas fa-plus fa-fw m-r1"></i>  Alterar informações da filial
      </h5>
    </template>

    <template #body>
      <BranchForm 
        :isEdit="true" 
        @updated="onUpdated" 
        :branch="branch" 
      />
    </template>
  </AppModal>
</template>

<script>
  import BranchForm from './BranchForm'

  export default {
    components: {
      BranchForm
    },
    data: function() {
      return {
        branch: null
      }
    },
    methods: {
      onUpdated() {
        this.$emit('refresh')

        $(this.$refs.modal.$el).modal('hide')

        this.$toast.success('Filial atualizada')
      }
    },
    mounted() {
      this.$on('branch-selected', branch => {
        this.branch = branch
      })

      $(this.$refs.modal.$el).on('hidden.bs.modal', () => {
        this.branch = null
      })
    }
  }
</script>