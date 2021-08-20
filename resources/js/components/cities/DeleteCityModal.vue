<template>
  <AppModal
    id="deleteCityModal"
    ref="modal"
    modal-header-class="bg-danger"
    modal-dialog-class="modal-dialog-centered"
  >
    <template #header>
      <h5 class="font-weight-bold text-white mb-0">
        <i class="fas fa-trash-alt fa-fw mr-1" />Deletar cidade
      </h5>
    </template>

    <template #body>
      <AppLoading v-if="isLoading" />

      <h5 class="text-center font-weight-bold">
        ATENÇÃO
      </h5>
      <div class="text-center mb-2">
        Selecione outra cidade para substituir a cidade que estão cadastrados os clientes de:
      </div>

      <h4 class="text-center text-success mb-0">
        <strong>{{ city.name }}</strong>
      </h4>
      <div
        v-if="city.state"
        class="text-center font-weight-bold"
      >
        {{ city.state.name }}
      </div>

      <form
        class="mt-3"
        @submit.prevent="onSubmit"
      >
        <label
          class="font-weight-bold"
          for="city"
        >Cidade: </label>

        <Multiselect
          v-model="form.city_id"
          :options="cities"
          :custom-label="({name, state}) => {
            return name + (state ? ' - ' + state.abbreviation : '')
          }"
          placeholder="Selecione a cidade"
          select-label="Selecionar"
          deselect-label="Remover"
          selected-label=" "
        >
          <div slot="noResult">
            Nenhuma cidade encontrada.
          </div>

          <div slot="noOptions">
            Nenhuma cidade cadastrada
          </div>
        </Multiselect>

        <small
          v-if="form.errors.has('city_id')"
          class="text-danger"
        >{{ form.errors.get('city_id') }}</small>

        <div class="d-flex mt-3">
          <button
            class="btn btn-block btn-success font-weight-bold mr-2"
            :disabled="form.isLoading"
          >
            <span
              v-if="form.isLoading"
              class="spinner-border spinner-border-sm mr-1"
            />SALVAR
          </button>
          <button
            data-dismiss="modal"
            class="btn btn-block btn-light"
          >
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
            isLoading: true,
            city: {},
            cities: [],
            form: new Form({
                city_id: '',
            })
        }
    },
    mounted() {
        this.$on('city-selected', city => {
            this.isLoading = true
            const index = this.cities.findIndex(_city => _city.id === city.id)

            axios.get('/gerenciamento/cidades/list', {
                params: {
                    only_names: true
                }
            })
                .then(response => {
                    this.cities = response.data.cities
                    this.city = city
                    this.cities.splice(index, 1, {...city, $isDisabled: true})
                })
                .catch(() => {})
                .then(() => {
                    this.isLoading = false
                })
        })

        $(this.$refs.modal.$el).on('hidden.bs.modal', () => {
            this.$emit('closed')
            this.form.reset()

            const index = this.cities.findIndex(_city => _city.id === this.city.id)
            this.cities.splice(index, 1, {...this.city, $$isDisabled: false})
        })
    },
    methods: {
        onSubmit() {
            this.form.isLoading = true
            this.form.submit('POST', `/gerenciamento/cidades/${this.city.id}/replace`)
                .then(() => {
                    $(this.$refs.modal.$el).modal('hide')
                    this.$emit('deleted')
                })
                .catch(() => {})
                .then(() => {
                    this.form.isLoading = false
                })
        }
    }
}
</script>
