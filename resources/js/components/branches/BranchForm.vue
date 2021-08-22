<template>
  <form @submit.prevent="onSubmit">
    <div class="form-group">
      <label
        for="branch"
        class="font-weight-bold"
      >Filial: </label>
      <Multiselect
        v-model="form.branch_id"
        :class="form.errors.has('branch_id') && 'is-invalid'"
        :options="cities"
        :custom-label="({name}) => `${name}`"
        placeholder="Selecione uma filial"
        select-label="Selecionar"
        deselect-label="Remover"
        selected-label=" "
        @open="form.errors.clear('branch_id')"
      >
        <div slot="noResult">
          Nenhuma filial encontrada.
        </div>
      </Multiselect>
      <small
        v-if="form.errors.has('branch_id')"
        class="text-danger d-block"
      >
        {{ form.errors.get('branch_id') }}
      </small>

      <small class="text-secondary">
        A lista de filiais é sincronizada com a lista de cidades cadastradas no sistema
      </small>
    </div>

    <div class="form-group">
      <label
        for="shippingCompanies"
        class="font-weight-bold"
      >Transportadora:</label>

      <Multiselect
        v-model="form.shipping_company_id"
        :class="form.errors.has('shipping_company_id') && 'is-invalid'"
        :options="shippingCompanies"
        :custom-label="({name}) => `${name}`"
        placeholder="Selecione uma transportadora"
        select-label="Selecionar"
        deselect-label="Remover"
        selected-label=" "
        @open="form.errors.clear('shipping_company_id')"
      >
        <div slot="noResult">
          Nenhuma transportadora encontrada.
        </div>

        <div slot="noOptions">
          Nenhuma transportadora cadastrada
        </div>
      </Multiselect>
      <small
        v-if="form.errors.has('shipping_company_id')"
        class="text-danger"
      >
        {{ form.errors.get('shipping_company_id') }}
      </small>
    </div>

    <div>
      <div class="font-weight-bold">
        Cidades:
      </div>

      <Multiselect
        v-model="form.cities_id"
        :class="form.errors.has('cities_id') && 'is-invalid'"
        :multiple="true"
        :options="cities"
        :custom-label="city => {
          return `${city.name}` + (showAlreadyOnBranch(city) ? ' (Já em uma filial)' : '')
        }"
        :close-on-select="false"
        placeholder="Selecione as cidades"
        select-label="Selecionar"
        deselect-label="Remover"
        selected-label=" "
        track-by="id"
        @open="form.errors.clear('cities_id')"
        @remove="onCityRemoved"
      >
        <div slot="noResult">
          Nenhuma cidade encontrada.
        </div>

        <div slot="noOptions">
          Nenhuma cidade cadastrada
        </div>
      </Multiselect>

      <small
        v-if="form.errors.has('cities_id')"
        class="text-danger d-block"
      >
        {{ form.errors.get('cities_id') }}
      </small>
      <div class="text-secondary small">
        Caso selecione uma cidade que já está em uma filial, ela será removida da filial que pertence.
      </div>
    </div>

    <div class="d-flex flex-row mt-3">
      <button
        type="submit"
        class="btn btn-block btn-success font-weight-bold mr-3"
        :disabled="form.isLoading"
      >
        <span
          v-if="form.isLoading"
          class="spinner-border spinner-border-sm mr-1"
        />
        {{ isEdit ? 'ATUALIZAR' : 'SALVAR' }}
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

<script>
import Form from '../../util/Form'
import Multiselect from 'vue-multiselect'
import _map from 'lodash/map'

export default {
  components: {
    Multiselect
  },
  props: {
    branch: undefined,
    isEdit: {
      type: Boolean,
      default: false
    }
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
        this.form.branch_id = this.cities.find(
          city => city.id === this.branch.city.id
        )

        this.form.shipping_company_id = this.shippingCompanies.find(
          company => company.id === this.branch.city.shipping_company.id
        )

        this.form.cities_id = this.branch.cities
      }
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

  },
  methods: {
    onCityRemoved(city) {
      const index = this.cities.findIndex(_city => _city.id === city.id)

      this.cities.splice(index, 1, {...city, branch: null})
    },
    showAlreadyOnBranch(city) {
      return city.branch_id && ! _map(this.form.cities_id, 'id').includes(city.id)
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
  }
}
</script>
