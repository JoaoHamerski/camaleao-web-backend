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
            <span class="font-weight-bold text-secondary sidebar-content-label">
              OCULTAR <span v-if="replaceBrandByNumber">NÚMERO</span> <span v-else> MARCA</span>
            </span>
          </label>
        </div>

        <div class="custom-control custom-checkbox">
          <input v-model="hideShield" type="checkbox" class="custom-control-input" id="hideShield">
          <label class="custom-control-label no-select" for="hideShield">
            <span class="font-weight-bold text-secondary sidebar-content-label">
              OCULTAR ESCUDO
            </span>
          </label>
        </div>
      </div>

      <div class="d-flex justify-content-around">
        <div class="custom-control custom-checkbox">
          <input v-model="replaceBrandByNumber" type="checkbox" class="custom-control-input" id="replaceBrandByNumber">
          <label class="custom-control-label no-select" for="replaceBrandByNumber">
            <span class="font-weight-bold text-secondary sidebar-content-label">SUBSTITUIR ESCUDO POR NÚMERO</span>
          </label>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import { shirtDefaultStateAfterClose } from '../../mixins';
  
  export default {
    mixins: [shirtDefaultStateAfterClose],
    computed: {
      hideBrand: {
        get() { return this.$store.state.hideBrand },
        set(value) { this.$store.commit('update', { hideBrand: value })}
      },
      hideShield: {
        get() { return this.$store.state.hideShield },
        set(value) { this.$store.commit('update', { hideShield: value })}
      },
      replaceBrandByNumber: {
        get() { return this.$store.state.replaceBrandByNumber },
        set(value) { this.$store.commit('update', { replaceBrandByNumber: value })}
      }
    },
    mounted() {
      $(this.$parent.$el).on(
        'shown.bs.dropdown', 
        () => { this.$store.commit('update', { isFront: true }) }
      );
    }
  }
</script>