<template>
  <form @submit.prevent="onSubmit">
    <div class="form-group">
      <label for="branch" class="font-weight-bold">Filial: </label>
      <Multiselect v-model="form.branch_id" 
        @open="form.errors.clear('branch_id')"
        :class="form.errors.has('branch_id') && 'is-invalid'"
        :options="cities"
        :custom-label="({name}) => `${name}`"
        placeholder="Selecione uma filial"
        selectLabel="Selecionar"
        deselectLabel="Remover"
        selectedLabel=" "
      >
        <div slot="noResult">
          Nenhuma filial encontrada.
        </div>
      </Multiselect>
      <small class="text-danger d-block" v-if="form.errors.has('branch_id')">
        {{ form.errors.get('branch_id') }}
      </small>

      <small class="text-secondary">
        A lista de filiais é sincronizada com a lista de cidades cadastradas no sistema
      </small>
    </div>

    <div class="form-group">
      <label for="shippingCompanies" class="font-weight-bold">Transportadora:</label>

      <Multiselect v-model="form.shipping_company_id" 
        @open="form.errors.clear('shipping_company_id')"
        :class="form.errors.has('shipping_company_id') && 'is-invalid'"
        :options="shippingCompanies"
        :custom-label="({name}) => `${name}`"
        placeholder="Selecione uma transportadora"
        selectLabel="Selecionar"
        deselectLabel="Remover"
        selectedLabel=" "
      >
        <div slot="noResult">
          Nenhuma transportadora encontrada.
        </div>

        <div slot="noOptions">
          Nenhuma transportadora cadastrada
        </div>
      </Multiselect>
      <small class="text-danger" v-if="form.errors.has('shipping_company_id')">
        {{ form.errors.get('shipping_company_id') }}
      </small>
    </div>

    <div>
      <div class="font-weight-bold">Cidades: </div>

      <Multiselect v-model="form.cities_id"
        @open="form.errors.clear('cities_id')"
        :class="form.errors.has('cities_id') && 'is-invalid'"
        :multiple="true" 
        :options="citiesWithDisabled"
        :custom-label="city => {
          return `${city.name}` + (showAlreadyOnBranch(city) ? ' (Já em uma filial)' : '')
        }"
        :closeOnSelect="false"
        placeholder="Selecione as cidades"
        selectLabel="Selecionar"
        deselectLabel="Remover"
        selectedLabel=" "
        trackBy="id"
        @remove="onCityRemoved"
      >
        <div slot="noResult">
          Nenhuma cidade encontrada.
        </div>

        <div slot="noOptions">
          Nenhuma cidade cadastrada
        </div>
      </Multiselect>

      <small class="text-danger d-block" v-if="form.errors.has('cities_id')">
        {{ form.errors.get('cities_id') }}
      </small>
      <small class="text-secondary">Você pode fazer múltiplas seleções</small>
    </div>

    <div class="d-flex flex-row mt-3">
      <button type="submit" class="btn btn-block btn-success font-weight-bold mr-3"
        :disabled="form.isLoading">
        <span class="spinner-border spinner-border-sm mr-1" v-if="form.isLoading"></span>
        {{ isEdit ? 'ATUALIZAR' : 'SALVAR' }}
      </button>
      <button data-dismiss="modal" class="btn btn-block btn-light">Cancelar</button>
    </div>
  </form>
</template>

<script>
  import Form from '../../util/Form'
  import Multiselect from 'vue-multiselect'
  import _map from 'lodash/map'

  export default {
    components: {
      Multiselect
    },
    props: {
      branch: { default: null },
      isEdit: { default: false }
    },
    data: function() {
      return {
        originalCities: [],
        cities: [],
        shippingCompanies: [],
        form: new Form({
          branch_id: '',
          shipping_company_id: '',
          cities_id: []
        }) 
      }
    },
    watch: {
      branch() {
        if (this.branch) {
          this.form.branch_id = this.branch.city
          this.form.shipping_company_id = this.branch.shipping_company
          this.form.cities_id = this.branch.cities
        }
      }
    },  
    computed: {
      citiesWithDisabled() {
        return this.cities.map(city => {
          return {
            ...city, 
            $isDisabled: (!! city.branch) && ! _map(this.form.cities_id, 'id').includes(city.id) 
          }
        })
      }
    },
    methods: {
      onCityRemoved(city, id) {
        let index = this.cities.findIndex(_city => _city.id === city.id)

        this.cities.splice(index, 1, {...city, branch: null})
      },
      showAlreadyOnBranch(city) {
         return city.branch && ! this.form.cities_id.includes(city)
      },
      onSubmit() {
        this.form.isLoading = true

        if (this.isEdit) {
          this.update()
        } else {
          this.create()
        }
      },
      create() {
        this.form.submit('POST', '/gerenciamento/filiais')
          .then(() => {
            this.$root.$emit('REFRESH_CITIES_LIST')
            this.$emit('created')
          })
          .catch(() => {})
          .then(() => {
            this.form.isLoading = false
          })
      },
      update() {
        this.form.submit('PATCH', `/gerenciamento/filiais/${this.branch.id}`)
          .then(() => {
            this.$root.$emit('REFRESH_CITIES_LIST')
            this.$emit('updated')
          })
          .catch(() => {})
          .then(() => {
            this.form.isLoading = false
          })
      }
    },
    mounted() {
      this.$on('modal-open', () => {
        if (this.originalCities.length) {
          this.cities = []
          this.cities.push(...this.originalCities)
        }
      })

      this.$root.$on('REFRESH_CITIES_LIST', () => {
        axios.get('/gerenciamento/cidades/list', {
          params: {
            only_names: true
          }
        })
          .then(response => {
            this.cities = response.data.cities
            this.originalCities = response.data.cities
          })
      })

      this.$root.$on('REFRESH_SHIPPING_COMPANIES_LIST', () => {
        axios.get('/transportadoras/list')
          .then(response => {
            this.shippingCompanies = response.data.companies
          })
      })

      this.$root.$emit('REFRESH_SHIPPING_COMPANIES_LIST')
      this.$root.$emit('REFRESH_CITIES_LIST')
      
    }
  }
</script>