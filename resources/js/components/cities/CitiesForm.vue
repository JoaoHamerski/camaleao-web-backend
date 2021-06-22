<template>
  <div>
    <form @submit.prevent="onSubmit">
      <div class="text-secondary mb-2">Selecione um estado para as cidades escolhidas:</div>
      <ul>
        <li v-for="city in cities" :key="city.id">{{ city.name }}</li>
      </ul>

      <div class="form-group mb-3">
        <label class="font-weight-bold" for="cities-states">Estado</label>
        <select name="states" id="cities-states" class="custom-select" v-model="form.state_id"
          :class="[form.errors.has('state_id') && 'is-invalid']"
        >
          <option value="">Selecione um estado</option>
          <option v-for="state in states" 
            :key="state.id" 
            :value="state.id"
          >{{ state.name }}</option>
        </select>
        <small class="text-danger" v-if="form.errors.has('state_id')">
          {{ form.errors.get('state_id') }}
        </small>
      </div>

      <div class="d-flex flex-row">
        <button type="submit" :disabled="form.isLoading" 
          class="btn btn-block btn-success font-weight-bold mr-2"
        >
          <span v-if="form.isLoading" class="spinner-border spinner-border-sm mr-1"></span>
          SALVAR
        </button>
        <button data-dismiss="modal" class="btn btn-block btn-light">Cancelar</button>
      </div>
    </form>
  </div>
</template>

<script>
  import Form from '../../util/Form'

  export default {
    data: function() {
      return {
        cities: [],
        states: [],
        form: new Form({
          state_id: '',
          cities_ids: []
        })
      }
    },
    methods: {
      onSubmit() {
        this.form.cities_ids = this.cities.map(city => city.id)

        this.form.isLoading = true
        
        this.form.submit('PATCH', '/gerenciamento/cidades')
          .then(() => {
            this.$emit('updated')
          }) 
          .catch(() => {})
          .then(() => {
            this.form.isLoading = false
          })
      }
    },
    mounted() {
      this.$on('cities-selected', cities => {
        this.cities = cities
      })
      
      axios.get('/gerenciamento/cidades/estados/list')
        .then(({data}) => {
          this.states = data.states
        })
    }
  }
</script>