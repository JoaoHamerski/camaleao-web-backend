<template>
  <AppModal :id="id" ref="modal" 
    modalDialogClass="modal-dialog-centered"
    modalHeaderClass="bg-primary"
  >
    <template #header>
      <h5 class="text-white font-weight-bold mb-0">
        <i class="fas fa-edit fa-fw mr-1"></i><slot name="title"></slot>
      </h5>
    </template>

    <template #body>
      <slot name="content"></slot>
    </template>
  </AppModal>
</template>

<script>
  export default {
    props: {
      id: { default: '' }
    },
    methods: {
      close() {
        $(this.$refs.modal.$el).modal('hide')
      }
    },
    mounted() {
      $(this.$refs.modal.$el).on('hide.bs.modal', () => {
        this.$emit('closed')
      })
    }
  }
</script>