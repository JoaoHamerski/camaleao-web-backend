<script>
import Form from '../../util/Form'

export default {
  props: {
    id: {
      type: String,
      default: ''
    }
  },
  data () {
    return {
      client: '',
      form: new Form({
        password: ''
      })
    }
  },
  mounted () {
    this.getClient()
  },
  methods: {
    onSubmit () {
      this.form.isLoading = true
      this.form.submit('delete', `/clientes/${this.id}`)
        .then(response => {
          window.location.href = response.redirect
        })
        .catch(() => {})
        .then(() => {
          this.form.password = ''
          this.form.isLoading = false
        })
    },
    getClient () {
      axios.get(`/clientes/${this.id}/json`)
        .then(response => {
          this.client = response.data.client
        })
    }
  }
}
</script>

<template>
  <AppModal
    id="clientDeleteModal"
    modal-header-class="bg-danger"
  >
    <template #header>
      <h5 class="text-white font-weight-bold mb-0">
        Deletar cliente
      </h5>
    </template>

    <template #body>
      <div class="text-center">
        <i class="fas fa-trash-alt fa-3x text-danger" />
      </div>

      <h4 class="text-center font-weight-bold mt-3">
        Você tem certeza?
      </h4>
      <div class="text-secondary text-center small">
        Todos os pedidos, pagamentos e anexos de pedidos serão deletados com o cliente.
      </div>

      <div class="my-4">
        <div class="text-center">
          Deletando:
        </div>
        <h4 class="text-center font-weight-bold text-primary">
          {{ client.name }}
        </h4>
      </div>

      <form
        data-type="vue"
        @submit.prevent="onSubmit"
        @focus.capture="form.errors.clear($event.target.name)"
      >
        <div class="form-group">
          <AppInput
            id="password"
            v-model="form.password"
            name="password"
            type="password"
            autocomplete="password"
            placeholder="Sua senha..."
            :error="form.errors.get('password')"
          >
            Digite sua senha para confirmar:
          </AppInput>
        </div>

        <div class="d-flex justify-content-between">
          <button
            class="btn btn-success font-weight-bold"
            type="submit"
            :disabled="form.isLoading"
          >
            <span
              v-if="form.isLoading"
              class="spinner-border spinner-border-sm"
            />
            CONFIRMAR
          </button>
          <button
            class="btn btn-light"
            data-dismiss="modal"
          >
            Fechar
          </button>
        </div>
      </form>
    </template>
  </AppModal>
</template>
