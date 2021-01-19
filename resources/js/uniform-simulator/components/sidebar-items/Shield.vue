<template>
  <div>
    <h6 class="sidebar-dropdown-title">
      GERAL
    </h6>
    <div class="sidebar-dropdown-content">
      <div class="d-flex justify-content-around">
        <div class="custom-control custom-checkbox">
          <input v-model="hideBrand" type="checkbox" class="custom-control-input" id="hideBrand">
          <label class="custom-control-label no-select" for="hideBrand">
            <span class="font-weight-bold text-secondary sidebar-content-label">OCULTAR MARCA</span>
          </label>
        </div>

        <div class="custom-control custom-checkbox">
          <input v-model="hideShield" type="checkbox" class="custom-control-input" id="hideShield">
          <label class="custom-control-label no-select" for="hideShield">
            <span class="font-weight-bold text-secondary sidebar-content-label">
              OCULTAR <span v-if="switchShieldToNumber">NÚMERO</span> <span v-else> ESCUDO</span>
            </span>
          </label>
        </div>
      </div>

      <div class="d-flex justify-content-around">
        <div class="custom-control custom-checkbox">
          <input v-model="switchShieldToNumber" type="checkbox" class="custom-control-input" id="switchShieldToNumber">
          <label class="custom-control-label no-select" for="switchShieldToNumber">
            <span class="font-weight-bold text-secondary sidebar-content-label">SUBSTITUIR ESCUDO POR NÚMERO</span>
          </label>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    mixins: [shirtDefaultStateAfterClose],
    data() {
      return {
        hideBrand: false,
        hideShield: false,
        switchShieldToNumber: false
      }
    },
    watch: {
      hideBrand: function(value) {
        EventBus.$emit('SHIRT_HIDE_BRAND', value);
      },
      hideShield: function(value) {
        EventBus.$emit('SHIRT_HIDE_SHIELD', value);
      },
      switchShieldToNumber: function(value) {
        EventBus.$emit('SHIRT_SWITCH_SHIELD', value);
      }
    },
    mounted() {
      $(this.$parent.$el).on(
        'shown.bs.dropdown', 
        () => { EventBus.$emit('SHIRT_FRONT_SHOW') }
      );

      $(this.$parent.$el).on(
        'hidden.bs.dropdown',
        () => { EventBus.$emit('SHIRT_DEFAULT_STATE') }
      );
    }
  }
</script>