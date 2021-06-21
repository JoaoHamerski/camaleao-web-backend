<template>
  <form @submit.prevent="onSubmit"
    @focus.capture="form.errors.clear($event.target.name)"
  >
    <div class="form-group">
      <label class="font-weight-bold" for="name">Nome da cidade</label>
      <input id="name"
        name="name" 
        class="form-control"
        :class="[form.errors.has('name') && 'is-invalid']"
        v-model="form.name" 
        type="text" 
      >
      <small class="text-danger font-weight-bold" 
        v-if="form.errors.has('name')"
      >
        {{ form.errors.get('name') }}
      </small>
    </div>

    <div class="form-group">
      <label for="states" class="font-weight-bold">Estado</label>
      <select id="states" 
        class="custom-select" 
        :class="[form.errors.has('state_id') && 'is-invalid']"
        v-model="form.state_id"
      >
        <option value="">Selecione um estado</option>
        <option v-for="state in states" 
          :value="state.id" 
          :key="state.id"
        >
          {{ state.name }}
        </option>
      </select>
      <small class="text-danger font-weight-bold" :class="[form.errors.has('state_id') && 'is-invalid']">
        {{ form.errors.get('state_id') }}
      </small>
    </div>

    <div class="d-flex flex-row">
      <button class="btn btn-block btn-success font-weight-bold" 
        type="submit"
        :disabled="form.isLoading"
      >
        <span v-if="form.isLoading" class="spinner-border spinner-border-sm mr-2"></span>SALVAR
      </button>
      <button class="btn btn-block btn-light">
        Cancelar
      </button>
    </div>
  </form>
</template>

<script>
  import Form from '../../util/Form'

  export default {
    props: {
      city: { default: null },
    },
    data: function() {
      return {
        states: [],
        form: new Form({
          name: this.city.name,
          state_id: this.city.state ? this.city.state.id : ''
        })
      }
    },
    methods: {
      onSubmit() {
        this.form.isLoading = true

        this.form.submit('patch', '/gerenciamento/cidades/' + this.city.id)
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
      axios.get('/gerenciamento/cidades/estados/list')
        .then(response => {
          this.states = response.data.states
        })
    }
  }
</script>