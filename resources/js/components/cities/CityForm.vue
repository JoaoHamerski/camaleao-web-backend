<template>
  <form @submit.prevent="onSubmit"
    @focus.capture="form.errors.clear($event.target.name)"
    data-type="vue"
  >
    <div class="form-group">
      <label class="font-weight-bold" :for="isEdit ? 'name-edit' : 'name'">
        Nome da cidade
      </label>
      <input v-model="form.name" 
        :id="isEdit ? 'name-edit' : 'name'"
        :name="isEdit ? 'name-edit' : 'name'" 
        class="form-control"
        :class="[form.errors.has('name') && 'is-invalid']"
        type="text" 
        placeholder="Digite o nome da cidade"
      >
      <small class="text-danger" 
        v-if="form.errors.has('name')"
      >
        {{ form.errors.get('name') }}
      </small>
    </div>
    
    <div class="form-group">
      <label :for="isEdit ? 'states-edit' : 'states'" class="font-weight-bold">
        Estado
      </label>
      <small class="text-secondary"> (opcional)</small>
      <select :id="isEdit ? 'states-edit' : 'states'" 
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
      <small class="text-danger" :class="[form.errors.has('state_id') && 'is-invalid']">
        {{ form.errors.get('state_id') }}
      </small>
    </div>

    <div class="d-flex flex-row">
      <button class="btn btn-block btn-success font-weight-bold mr-2" 
        type="submit"
        :disabled="form.isLoading"
      >
        <span v-if="form.isLoading" class="spinner-border spinner-border-sm mr-2"></span>SALVAR
      </button>

      <button type="button" data-dismiss="modal" class="btn btn-block btn-light">
        Cancelar
      </button>
    </div>
  </form>
</template>

<script>
  import Form from '../../util/Form'
  import Multiselect from 'vue-multiselect'


  export default {
    components: {
      Multiselect
    },
    props: {
      isEdit: { default: false },
    },
    data: function() {
      return {
        city: null,
        states: [],
        form: new Form({
          name: '',
          state_id: ''
        })
      }
    },
    methods: {
      onSubmit() {
        this.form.isLoading = true

        if (this.isEdit) {
          this.update()
        } else {
          this.create()
        }
      },
      create() {
        this.form.isLoading = true

        this.form.submit('POST', '/gerenciamento/cidades/')
          .then(response => {
            console.log(response)
            this.form.reset()
            this.$toast.success('Cidade cadastrada')
            this.$emit('created', response.city)
          })
          .catch(error => {
            console.log(error.response)
          })
          .then(() => {
            this.form.isLoading = false
          })
      },
      update() {
        this.form.submit('PATCH', '/gerenciamento/cidades/' + this.city.id)
          .then(() => {
            this.$toast.success('Cidade atualizada')
            this.$emit('updated')
          })
          .catch(() => {})
          .then(() => {
            this.form.isLoading = false
          })
      }
    },
    mounted() {
      this.$on('city-selected', city => {
        this.city = city
        this.form.name = city.name
        this.form.state_id = this.city.state ? this.city.state.id : ''
      })

      this.$on('pre-form', search => {
        this.form.name = search
      })

      axios.get('/gerenciamento/cidades/estados/list')
        .then(response => {
          this.states = response.data.states
        })
    }
  }
</script>