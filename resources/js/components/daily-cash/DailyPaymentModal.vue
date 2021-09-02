<script>
import DailyPaymentForm from './DailyPaymentForm.vue'

export default {
  components: {
    DailyPaymentForm
  },
  methods: {
    onPaymentCreated () {
      $(this.$refs.modal.$el).modal('hide')
      this.$emit('created')
    }
  }
}
</script>

<template>
  <AppModal
    id="dailyPaymentModal"
    ref="modal"
    modal-header-class="bg-primary"
  >
    <template #header>
      <h5 class="font-weight-bold text-white mb-0">
        <i class="fas fa-plus fa-fw mr-1" />Nova entrada
      </h5>
    </template>

    <template #body>
      <DailyPaymentForm @created="onPaymentCreated" />
    </template>
  </AppModal>
</template>
