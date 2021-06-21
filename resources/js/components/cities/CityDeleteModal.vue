<template>
  <AppModal id="cityDeleteModal"
    ref="modal"
    modalHeaderClass="bg-danger"
    modalDialogClass="modal-dialog-centered"
  >
    <template #header>
      <h5 class="font-weight-bold text-white mb-0">
        <i class="fas fa-trash-alt fa-fw mr-1"></i>Deletar cidade
      </h5>
    </template>

    <template #body>
      <h5 class="text-center font-weight-bold">ATENÇÃO</h5>
      <div class="text-center mb-2">
        Selecione outra cidade para substituir a cidade que estão cadastrados os clientes de: 
      </div>
      <h4 class="text-center text-success"> <strong>{{ city.name }}</strong></h4>

      <form @submit.prevent="onSubmit">
        <label class="font-weight-bold" for="city">Cidade: </label>
        <select name="city" 
          id="city" 
          class="custom-select" 
          v-model="form.city_id" 
          :class="[form.errors.has('city_id') && 'is-invalid']"
        >
          <option value="">Selecione a cidade</option>
          <option v-for="city in cities" :key="city.id" 
            :value="city.id"
          >
            {{ city.name }} {{ city.state ? ' | ' + city.state.abbreviation : '' }}
          </option>
        </select>
        <small class="text-danger" 
          v-if="form.errors.has('city_id')"
        >{{ form.errors.get('city_id') }}</small>

        <div class="d-flex mt-3">
          <button class="btn btn-block btn-success font-weight-bold mr-2" 
            :disabled="form.isLoading"
          >
            <span v-if="form.isLoading" 
              class="spinner-border spinner-border-sm mr-1"></span>SALVAR
          </button>
          <button data-dismiss="modal" class="btn btn-block btn-light">
            Cancelar
          </button>
        </div>
      </form>
    </template>
  </AppModal>
</template>

<script>
  import Form from '../../util/Form'

  export default {
    props: {
      city: { default: null },
      cities: { default: [] }
    },
    data: function() {
      return {
        form: new Form({
          city_id: '',
        })
      }
    },
    methods: {
      onSubmit() {
        this.form.isLoading = true
        this.form.submit('POST', `/gerenciamento/cidades/${this.city.id}/replace`)
          .then(response => {
            $(this.$refs.modal.$el).modal('hide')
            this.$emit('deleted')
          })
          .catch(() => {})
          .then(() => {
            this.form.isLoading = false
          })
      }
    },
    mounted() {
      $(this.$refs.modal.$el).on('hide.bs.modal', () => {
        this.$emit('closed')
      })
    }
  }
</script>