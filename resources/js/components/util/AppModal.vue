<template>
  <div class="modal fade" :id="id" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" :class="modalDialogClass">
      <div class="modal-content">
        <slot name="content"></slot>

        <div class="modal-header" v-if="!! this.$slots.header" :class="modalHeaderClass">
          <slot name="header"></slot>
        </div>

        <div class="modal-body">
          <slot name="body"></slot>
        </div>

        <div class="modal-footer bg-light d-block" v-if="!! this.$slots.footer">
          <slot name="footer"></slot>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    props: {
      id: '',
      modalDialogClass: '',
      modalHeaderClass: '',
    }
  }
</script>