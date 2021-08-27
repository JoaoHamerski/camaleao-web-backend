<script>
export default {
  props: {
    id: {
      type: [String, Number],
      default: ''
    },
    modalDialogClass: {
      type: String,
      default: ''
    },
    modalHeaderClass: {
      type: String,
      default: ''
    },
  },
  mounted () {
    $(this.$refs.modal).on('hide.bs.modal', () => {
      this.$emit('closing')
    })

    $(this.$refs.modal).on('hidden.bs.modal', () => {
      this.$emit('closed')
    })
  },
  methods: {
    open() {
      $(this.$refs.modal).modal('show')
    },
    close () {
      $(this.$refs.modal).modal('hide')
    }
  }
}
</script>

<template>
  <div
    :id="id"
    ref="modal"
    class="modal fade"
    tabindex="-1"
    aria-hidden="true"
  >
    <div
      class="modal-dialog"
      :class="modalDialogClass"
    >
      <div class="modal-content">
        <slot name="content" />

        <div
          v-if="!! $slots.header"
          class="modal-header"
          :class="modalHeaderClass"
        >
          <slot name="header" />
        </div>

        <div class="modal-body">
          <slot name="body" />
        </div>

        <div
          v-if="!! $slots.footer"
          class="modal-footer bg-light d-block"
        >
          <slot name="footer" />
        </div>
      </div>
    </div>
  </div>
</template>
