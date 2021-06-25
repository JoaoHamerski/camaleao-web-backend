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
      
      <h4 class="text-center text-success mb-0"> 
        <strong>{{ city.name }}</strong>
      </h4>
      <div class="text-center font-weight-bold" v-if="city.state">{{ city.state.name }}</div>

      <form @submit.prevent="onSubmit" class="mt-3">
        <label class="font-weight-bold" for="city">Cidade: </label>

        <Multiselect v-model="form.city_id"
          :options="cities"
          :custom-label="({name, state}) => {
            return name + (state ? ' - ' + state.abbreviation : '')
          }"
          placeholder="Selecione a cidade"
          selectLabel="Selecionar"
          deselectLabel="Remover"
          selectedLabel=" "
        >
          <div slot="noResult">
            Nenhuma cidade encontrada.
          </div>

          <div slot="noOptions">
            Nenhuma cidade cadastrada
          </div>
        </Multiselect>

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
  import Multiselect from 'vue-multiselect'

  export default {
    components:{
      Multiselect
    },
    data: function() {
      return {
        city: {},
        cities: [],
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
      this.$on('city-selected', city => {
        let index = this.cities.findIndex(_city => _city.id === city.id)

        this.city = city
        this.cities.splice(index, 1, {...city, $isDisabled: true})
      })

      $(this.$refs.modal.$el).on('hidden.bs.modal', () => {
        this.$emit('closed')
        this.form.reset()

        let index = this.cities.findIndex(_city => _city.id === this.city.id)
        this.cities.splice(index, 1, {...this.city, $$isDisabled: false})
      })

      axios.get('/gerenciamento/cidades/list', {
        params: {
          only_names: true
        }
      })
        .then(response => {
          this.cities = response.data.cities
        })
    }
  }
</script>