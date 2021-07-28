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
        ref="clientForm"
        :isEdit="isEdit" 
        :id="id"
        @open-city-modal="openCityModal"
        @loading="isLoading = $event"
      />
    </template>

    <template #footer>
      <div class="d-flex justify-content-between">
        <button class="btn btn-success font-weight-bold" 
          @click="submitForm"
          :disabled="isLoading"
        >
          <span class="spinner-border spinner-border-sm mr-1" 
            v-if="isLoading"
          ></span>
          {{ isEdit ? 'Atualizar' : 'Cadastrar' }}
        </button>
        <button class="btn btn-light" data-dismiss="modal">Fechar</button>
      </div>
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
        isOpen: false,
        isLoading: false
      }
    },
    methods: {
      submitForm() {
        this.$refs.clientForm.onSubmit()
      },
      openCityModal(search) {
        $(this.$refs.modal.$el).modal('hide')
        this.$parent.$refs.newCityModal.$emit('pre-form', search)
        $(this.$parent.$refs.newCityModal.$el).modal('show')
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