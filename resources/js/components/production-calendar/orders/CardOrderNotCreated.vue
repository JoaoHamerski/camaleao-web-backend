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
  methods: {
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

    <div v-viewer="viewerConfig">
      <img
        v-if="imagePath"
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
        class="form-control form-control-sm"
        rows="4"
        placeholder="Digite um lembrete..."
      />
    </div>

    <div class="card-footer p-0">
      <div class="btn-group d-flex">
        <button
          class="btn btn-success btn-block font-weight-bold radius-0"
        >
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
