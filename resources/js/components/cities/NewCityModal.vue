<template>
  <AppModal
    id="newCityModal"
    ref="modal"
    modal-dialog-class="modal-dialog-centered"
    modal-header-class="bg-success"
  >
    <template #header>
      <h5 class="text-white font-weight-bold mb-0">
        <i class="fas fa-plus fa-fw mr-1" />Nova cidade
      </h5>
    </template>

    <template #body>
      <CityForm
        ref="cityForm"
        @created="onCreate"
      />
    </template>
  </AppModal>  
</template>

<script>
import CityForm from './CityForm'
  
export default {
  components: {
    CityForm
  },
  mounted() {
    this.$on('pre-form', search => {
      this.$refs.cityForm.$emit('pre-form', search)
      $(this.$refs.modal.$el).modal('show')

    })
  },
  methods: {
    onCreate(city) {
      $(this.$refs.modal.$el).modal('hide')
      this.$emit('created', city)
    }
  }
}
</script>