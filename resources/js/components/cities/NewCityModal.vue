<template>
  <AppModal id="newCityModal"
    ref="modal"
    modalDialogClass="modal-dialog-centered"
    modalHeaderClass="bg-success"
  >
    <template #header>
      <h5 class="text-white font-weight-bold mb-0">
        <i class="fas fa-plus fa-fw mr-1"></i>Nova cidade
      </h5>
    </template>

    <template #body>
      <CityForm @created="onCreate" ref="cityForm"/>
    </template>
  </AppModal>  
</template>

<script>
  import CityForm from './CityForm'
  
  export default {
    components: {
      CityForm
    },
    methods: {
      onCreate(city) {
        $(this.$refs.modal.$el).modal('hide')
        this.$emit('created', city)
      }
    },
    mounted() {
      this.$on('pre-form', search => {
        this.$refs.cityForm.$emit('pre-form', search)
        $(this.$refs.modal.$el).modal('show')

      })
    }
  }
</script>