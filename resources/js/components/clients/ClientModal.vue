<template>
  <AppModal id="clientModal"
    ref="modal"
    modalDialogClass="modal-dialog-centered"
    :modalHeaderClass="isEdit ? 'bg-primary' : 'bg-success'"
  >
    <template #header>
      <h5 class="mb-0 font-weight-bold text-white"> 
        <i class="fas fa-user" v-if="isEdit"></i>
        <i v-else class="fas fa-user-plus"></i>
        {{ ! isEdit ? 'Novo cliente' : 'Alterar dados' }}
      </h5>
    </template>

    <template #body>
      <ClientForm v-if="isOpen"
        :isEdit="isEdit" 
        :id="id"
      />
    </template>
  </AppModal>
</template>

<script>
  import ClientForm from './ClientForm.vue'

  export default {
    components: {
      ClientForm
    },
    props: {
      id: { default: '' },
      isEdit: { default: false }
    },
    data: () => {
      return {
        isOpen: false
      }
    },
    mounted() {
      $(this.$refs.modal.$el).on('show.bs.modal', () => {
        if (! this.isOpen) {
          this.isOpen = true
        }
      })
    }
  }
</script>