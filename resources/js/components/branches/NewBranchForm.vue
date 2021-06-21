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

      <small class="text-secondary">A lista de filiais é sincronizada com a lista de cidades cadastradas no sistema</small>
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
        :options="cities"
        :custom-label="({id, name}) => `${name}`"
        :closeOnSelect="false"
        placeholder="Selecione as cidades"
        selectLabel="Selecionar"
        deselectLabel="Remover"
        selectedLabel=" "
        trackBy="id"
        return="id"
      >
        <div slot="noResult">
          Nenhuma cidade encontrada.
        </div>https://simulador.camaleaocamisas.com.br/

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
        <span class="spinner-border spinner-border-sm" v-if="form.isLoading"></span>SALVAR
      </button>
      <button data-dismiss="modal" class="btn btn-block btn-light">Cancelar</button>
    </div>
  </form>
</template>

<script>
  import Form from '../../util/Form'
  import Multiselect from 'vue-multiselect'
  import cloneDeep from 'lodash/cloneDeep'

  export default {
    components: {
      Multiselect
    },
    data: function() {
      return {
        cities: [],
        shippingCompanies: [],
        form: new Form({
          branch_id: '',
          shipping_company_id: '',
          cities_id: []
        })
      }
    },
    methods: {
      onSubmit() {
        let form = cloneDeep(this.form)

        this.form.isLoading = true

        this.form.submit('POST', '/gerenciamento/filiais')
          .then(() => {
            this.$emit('created')
          })
          .catch(error => {
            console.log(error.response)
          })
          .then(() => {
            this.form.isLoading = false
          })
      } 
    },
    mounted() {
      axios.get('/gerenciamento/cidades/list')
        .then(response => {
          this.cities = response.data.cities
        })
      
      axios.get('/transportadoras/list')
        .then(response => {
          this.shippingCompanies = response.data.companies
        })
    }
  }
</script>