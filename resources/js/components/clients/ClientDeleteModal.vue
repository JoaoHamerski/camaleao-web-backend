<template>
  <AppModal id="clientDeleteModal"
    modalHeaderClass="bg-danger"
  >
    <template #header>
      <h5 class="text-white font-weight-bold mb-0">Deletar cliente</h5>
    </template>

    <template #body>
      <div class="text-center">
        <i class="fas fa-trash-alt fa-3x text-danger"></i>
      </div>

      <h4 class="text-center font-weight-bold mt-3">Você tem certeza?</h4>
      <div class="text-secondary text-center small">
        Todos os pedidos, pagamentos e anexos de pedidos serão deletados com o cliente.
      </div>

      <div class="my-4">
        <div class="text-center">Deletando:</div>
        <h4 class="text-center font-weight-bold text-primary">{{ client.name }}</h4>
      </div>

      <form @submit.prevent="onSubmit" 
        @focus.capture="form.errors.clear($event.target.name)"
        data-type="vue"
      >
        <div class="form-group">
          <AppInput v-model="form.password"
            id="password"
            name="password"
            type="password"
            autocomplete="password"
            placeholder="Sua senha..."
            :error="form.errors.get('password')"
          >Digite sua senha para confirmar:</AppInput>
        </div>
        
        <div class="d-flex justify-content-between">
          <button class="btn btn-success font-weight-bold" 
            type="submit"
            :disabled="form.isLoading"
          >
            <span v-if="form.isLoading" class="spinner-border spinner-border-sm"></span>
            CONFIRMAR
          </button>
          <button class="btn btn-light" data-dismiss="modal">Fechar</button>
        </div>
      </form>
    </template>
  </AppModal>
</template>

<script>
  import Form from '../../util/Form'

  export default {
    props: {
      id: { default: ''}
    },
    data() {
      return {
        client: '',
        form: new Form({
          password: ''
        })
      }
    },
    methods: {
      onSubmit() {
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
      getClient() {
        axios.get(`/clientes/${this.id}/json`)
          .then(response => {
            this.client = response.data.client
          })
      } 
    },
    mounted() {
      this.getClient()
    }
  }
</script>