<template>
  <form @submit.prevent="onSubmit"
    @focus.capture="form.errors.clear($event.target.name)"
    data-type="vue"
  >
    <div class="d-flex form-row">
      <div class="col">
        <AppInput v-model="form.name" 
          id="name"
          name="name"
          placeholder="Novo tipo de camisa"
          :error="form.errors.get('name')"
        />
      </div>

      <div>
        <button class="btn btn-primary font-weight-bold" 
          type="submit"
          :disabled="form.isLoading"
        >
          <span v-if="form.isLoading" class="spinner-border spinner-border-sm mr-1"></span>
          Adicionar
        </button>
      </div>
    </div>
  </form>
</template>

<script>
  import Form from '../../util/Form'

  export default {
    data: () => {
      return {
        form: new Form({
          name: ''
        })
      }
    },
    methods: {
      create() {
        this.form.isLoading = true

        this.form.submit('POST', '/tipos-de-roupas')
          .then(response => {
            this.$emit('created')
            this.form.reset()
            this.$toast.success('Novo tipo de roupa criado')
          })
          .catch(error => {
            console.log(error)
          })
          .then(() => {
            this.form.isLoading = false
          })
      },
      onSubmit() {
        this.$modal.fire({
          icon: 'info',
          iconHtml: '<i class="fas fa-exclamation-circle"></i>',
          iconColor: '#3490dc',
          title: 'Você tem certeza?',
          html: 'Ao criar um novo tipo de roupa, <strong>ele não poderá ser deletado</strong>, apenas ocultado.'
        })
          .then(response => {
            if (response.isConfirmed) {
              this.create()
            }
          })
      }
    }
  }
</script>