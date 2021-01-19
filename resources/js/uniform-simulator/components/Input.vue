<template>
  <div>
    <label>Phone Number</label>
    <masked-input
      type="text"
      name="phone"
      class="form-control"
      v-model="phone"
      :mask="['(', /[1-9]/, /\d/, /\d/, ')', ' ', /\d/, /\d/, /\d/, '-', /\d/, /\d/, /\d/, /\d/]"
      :guide="false"
      placeholderChar="#">
    </masked-input>
  </div>
</template>

<script>
  import MaskedInput from 'vue-text-mask'

  export default {
    name: 'name',

    components: {
      MaskedInput
    },

    data () {
      return {
        phone: ''
      }
    }
  }
</script>