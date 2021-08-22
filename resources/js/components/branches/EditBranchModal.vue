<template>
  <AppModal
    id="editBranchModal" 
    ref="modal"
    modal-dialog-class="modal-dialog-centered"
    modal-header-class="bg-primary text-white"
  >
    <template #header>
      <h5 class="font-weight-bold mb-0">
        <i class="fas fa-plus fa-fw m-r1" />  Alterar informações da filial
      </h5>
    </template>

    <template #body>
      <BranchForm
        ref="branchForm"
        :is-edit="true" 
        :branch="branch" 
        @updated="onUpdated" 
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
  mounted() {
    this.$on('branch-selected', branch => {
      this.branch = branch
    })

    $(this.$refs.modal.$el).on('hidden.bs.modal', () => {
      this.branch = null
    })

    $(this.$refs.modal.$el).on('show.bs.modal', () => {
      this.$refs.branchForm.$emit('modal-open')
    })
  },
  methods: {
    onUpdated() {
      this.$emit('refresh')

      $(this.$refs.modal.$el).modal('hide')

      this.$toast.success('Filial atualizada')
    }
  }
}
</script>