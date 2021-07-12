<template>
  <form @submit.prevent="onSubmit" class="position-relative">
    <AppLoading v-if="isLoading" />

    <div class="form-group">
      <label class="font-weight-bold" for="name">Nome: </label>
      <AppInput v-model="form.name" 
        id="name" 
        name="name" 
        placeholder="Digite o nome do cliente..."
        :error="form.errors.get('name')"
      />
    </div>

    <div class="form-group">
      <label class="font-weight-bold" for="phone">Telefone: </label>
      <AppInput v-model="form.phone" 
        id="phone" 
        name="phone" 
        placeholder="Digite o telefone..."
        :mask="masks.phone"
        :error="form.errors.get('phone')"
      />
    </div>

    <div class="form-group">
      <label class="font-weight-bold" for="cities">Cidade: </label>
      <Multiselect v-model="form.city_id"
        ref="cityMultiselect"
        :class="form.errors.has('city') && 'is-invalid'"
        id="cities"
        :options="cities"
        :custom-label="({name}) => name"
        placeholder="Selecione uma cidade"
        selectLabel="Selecionar"
        deselectLabel="Remover"
        selectedLabel=" "
        @select="onCitySelected"
      >
        <div slot="noResult">
          Nenhuma cidade encontrada, 
          <a href="" 
            class="font-weight-bold" 
            @click.prevent="$emit('open-city-modal', $refs.cityMultiselect.search)">cadastrar nova</a>.
        </div>

        <div slot="noOptions">
          Nenhuma cidade cadastrada
        </div>
      </Multiselect>
      <small class="text-secondary">
        Ao selecionar a <strong>cidade,</strong> a <strong>filial</strong> e o <strong>frete</strong> serão preenchidos automaticamente com os dados relacionados, mas ainda é possível alterá-los caso precise.
      </small>
    </div>

    <div class="form-group">
      <label class="font-weight-bold" for="branches">Filial: </label>
      <Multiselect v-model="form.branch_id"
        :class="form.errors.has('branch') && 'is-invalid'"
        id="branches"
        :options="branches"
        :custom-label="({city}) => city ? city.name : '[cidade da filial deletada]'"
        placeholder="Selecione uma filial"
        selectLabel="Selecionar"
        deselectLabel="Remover"
        selectedLabel=" "
      >
        <div slot="noResult">
          Nenhuma filial encontrada.
        </div>

        <div slot="noOptions">
          Nenhuma filial cadastrada
        </div>
      </Multiselect>
      <div v-if="form.city_id && form.branch_id === ''" class="small text-secondary">
        Nenhuma filial relacionada a cidade selecionada foi encontrada
      </div>
    </div>

    <div class="form-group">
      <label class="font-weight-bold" for="shippingCompanies">Frete: </label>
      <Multiselect v-model="form.shipping_company_id"
        :class="form.errors.has('shipping_company') && 'is-invalid'"
        id="shippingCompanies"
        :options="shipping_companies"
        :custom-label="({name}) => name"
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
      <div v-if="form.city_id && form.shipping_company_id === ''" class="small text-secondary">
        Nenhuma transportada relacionada a cidade selecionada foi encontrada
      </div>
    </div>

    <div class="d-flex justify-content-between">
      <button class="btn btn-success font-weight-bold"
        :disabled="form.isLoading"
        type="submit"
      >
        <span class="spinner-border spinner-border-sm mr-1" 
          v-if="form.isLoading"
        ></span>
        {{ isEdit ? 'Atualizar' : 'Cadastrar' }}
      </button>

      <button class="btn btn-light"
        type="button"
        data-dismiss="modal"
      >Fechar</button>
    </div>
  </form>
</template>

<script>
  import Form from '../../util/Form'
  import masks from '../../util/masks'
  import Multiselect from 'vue-multiselect'
  import _map from 'lodash/map'

  export default {
    components: {
      Multiselect
    },
    props: {
      id: { default: ''},
      isEdit: { default: false }
    },
    data: function() {
      return {
        masks,
        isLoading: false,
        branches: [],
        shipping_companies: [],
        cities: [],
        form: new Form({
          name: '',
          phone: '',
          branch_id: '',
          city_id: '',
          shipping_company_id: ''
        })
      }
    },
    methods: {
      onCitySelected(city) {
        this.form.branch_id = ''
        this.form.shipping_company_id = ''

        if (city.branch_id) {
          this.form.branch_id = this.branches.find(
            branch => branch.id === city.branch_id
          )
        }
        
        if (city.shipping_company) {
          this.form.shipping_company_id = this.shipping_companies.find(
            company => company.id === city.shipping_company.id
          )
        }
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
        this.form.submit('POST', '/clientes')
          .then(() => {
            location.reload()
          })
          .catch(() => {})
          .then(() => {
            this.form.isLoading = false
          })
      },
      update() {
        this.form.submit('PATCH', `/clientes/${this.id}/`)
          .then(() => {
            location.reload()
          })
          .catch(() => {})
          .then(() => {
            this.form.isLoading = false
          })
      },  
      populateBranches() {
        return new Promise((resolve, reject) => {
          axios.get('/gerenciamento/filiais/list', {
            params: {
              no_paginate: true
            }
          })
            .then(response => {
              this.branches.push(...response.data.branches)
              resolve()
            })
        })
      },
      populateShippingCompanies() {
        return new Promise((resolve, reject) => {
          axios.get('/transportadoras/list')
            .then(response => {
              this.shipping_companies.push(...response.data.companies)
              resolve()
            })
        })
      },
      populateCities() {
        return new Promise((resolve, reject) => {
          axios.get('/gerenciamento/cidades/list')
            .then(response => {
              this.cities.push(...response.data.cities)
              resolve()
            })
        })
      },
      populateForm() {
        return new Promise((resolve, reject) => {
          axios.get(`/clientes/${this.id}/json`)
            .then(response => {
              let client = response.data.client

              this.form.name = client.name
              this.form.phone = client.phone

              if (client.city_id) {
                this.form.city_id = this.cities.find(
                  city => city.id === client.city.id
                )
              }

              if (client.shipping_company_id) {
                this.form.shipping_company_id = this.shipping_companies.find(
                  company => company.id === client.shipping_company_id
                )
              }

              if (client.branch_id) {
                this.form.branch_id = this.branches.find(
                  branch => branch.id === client.branch_id
                )
              }

              resolve()
            })
        })
      },
      async populateData() {
        this.isLoading = true

        await this.populateBranches()
        await this.populateShippingCompanies()
        await this.populateCities()

        if (this.isEdit) {
          await this.populateForm()
        }

        this.isLoading = false
      }
    },
    mounted() {
      this.populateData()

      this.$on('city-created', async (city) => {
        this.cities = []
        this.populateCities()
          .then(() => {
            this.form.city_id = this.cities.find(
              _city => _city.id === city.id
            )
          })
      })
    }
  }
</script>