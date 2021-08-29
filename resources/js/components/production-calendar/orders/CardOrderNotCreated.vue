<script>
export default {
  props: {
    imagePath: undefined,
    order: {
      type: Object,
      required: true
    },
    viewerConfig: {
      type: Object,
      required: true
    }
  },
  data () {
    return {
      reminder: '',
      isLoading: false
    }
  },
  methods: {
    create () {
      this.isLoading = true
      axios.post('/calendario-de-producao/pedidos/novo', {
        ...this.order,
        reminder: this.reminder
      })
        .then(response => {
          this.$toast.success('Pedido pre-registrado com sucesso!')
          this.$emit('created', response.data.order)
        })
        .catch(error => {
          console.log(error.response)
        })
        .then(() => {
          this.isLoading = false
        })
    },
    handleCancel () {
      this.$emit('cancel')
    }
  }
}
</script>

<template>
  <div class="card card-order">
    <div class="card-header py-1 font-weight-bold bg-camaleao text-white">
      PRÉ-REGISTRO
    </div>

    <div
      v-viewer="viewerConfig"
      class="text-center"
    >
      <img
        v-if="imagePath"
        :style="{maxHeight: '175px', maxWidth: '100%', width: 'auto'}"
        :src="imagePath"
        class="py-3 card-img-top clickable"
      >
    </div>

    <div class="m-2">
      <label
        for="reminder"
        class="small"
      >
        <span class="font-weight-bold">Lembrete</span>
        <span class="text-secondary">(opcional)</span>
      </label>

      <textarea
        id="reminder"
        v-model="reminder"
        class="form-control form-control-sm"
        rows="4"
        placeholder="Digite um lembrete..."
      />
    </div>

    <div class="card-footer p-0">
      <div class="btn-group d-flex">
        <button
          :disabled="isLoading"
          class="btn btn-success btn-block font-weight-bold radius-0 text-nowrap px-0"
          @click="create"
        >
          <span
            v-show="isLoading"
            class="spinner-border spinner-border-sm"
          />
          Confirmar
        </button>

        <button
          class="btn btn-light btn-block"
          @click="handleCancel"
        >
          Cancelar
        </button>
      </div>
    </div>
  </div>
</template>
