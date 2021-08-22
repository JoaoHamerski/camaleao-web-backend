<template>
  <AppModal
    id="clientModal"
    ref="modal"
    modal-dialog-class="modal-dialog-centered"
    :modal-header-class="isEdit ? 'bg-primary' : 'bg-success'"
  >
    <template #header>
      <h5 class="mb-0 font-weight-bold text-white">
        <i
          v-if="isEdit"
          class="fas fa-user"
        />
        <i
          v-else
          class="fas fa-user-plus"
        />
        {{ ! isEdit ? 'Novo cliente' : 'Alterar dados' }}
      </h5>
    </template>

    <template #body>
      <ClientForm
        v-if="isOpen"
        :id="id"
        ref="clientForm"
        :is-edit="isEdit"
        @open-city-modal="openCityModal"
        @loading="isLoading = $event"
      />
    </template>

    <template #footer>
      <div class="d-flex justify-content-between">
        <button
          class="btn btn-success font-weight-bold"
          :disabled="isLoading"
          @click="submitForm"
        >
          <span
            v-if="isLoading"
            class="spinner-border spinner-border-sm mr-1"
          />
          {{ isEdit ? 'Atualizar' : 'Cadastrar' }}
        </button>
        <button
          class="btn btn-light"
          data-dismiss="modal"
        >
          Fechar
        </button>
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
    id: {
      type: [String, Number],
      default: ''
    },
    isEdit: {
      type: Boolean,
      default: false
    }
  },
  data: () => {
    return {
      isOpen: false,
      isLoading: false
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
  }
}
</script>
