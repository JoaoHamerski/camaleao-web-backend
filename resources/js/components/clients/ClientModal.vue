<template>
  <AppModal id="clientModal"
    ref="modal"
    modalDialogClass="modal-dialog-centered modal-dialog-scrollable"
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
        ref="clientForm"
        :isEdit="isEdit" 
        :id="id"
        @open-city-modal="openCityModal"
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
    methods: {
      openCityModal(search) {
        $(this.$refs.modal.$el).modal('hide')
        this.$parent.$refs.newCityModal.$emit('pre-form', search)
        // $(this.$parent.$refs.newCityModal.$el).modal('show')
      }
    },
    mounted() {
      this.$on('city-created', city => {
        $(this.$refs.modal.$el).modal('show')
        this.$refs.clientForm.$emit('city-created', city)
      })

      $(this.$refs.modal.$el).on('show.bs.modal', () => {
        if (! this.isOpen) {
          this.isOpen = true
        }
      })
    }
  }
</script>